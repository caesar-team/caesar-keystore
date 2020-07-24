<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Caesar\CaesarServerRpcInterface;
use App\Controller\AbstractController;
use App\Factory\View\KeysViewFactory;
use App\Factory\View\PublicKeyViewFactory;
use App\Form\Request\PublicKeysType;
use App\Form\Type\SaveKeysType;
use App\Keys\KeysModifier;
use App\Repository\KeyRepositoryInterface;
use App\Request\KeysRequest;
use App\Request\PublicKeysRequest;
use App\View\KeysView;
use App\View\PublicKeyView;
use Fourxxi\RestRequestError\Exception\FormInvalidRequestException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route(path="/keys")
 * @SWG\Response(response=401, description="Unauthorized")
 */
class KeysController extends AbstractController
{
    /**
     * Get self keys.
     *
     * @SWG\Tag(name="Keys")
     * @SWG\Response(
     *     response=200,
     *     description="List of user keys",
     *     @Model(type=KeysView::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="User has no keys"
     * )
     *
     * @Route(
     *     path="",
     *     name="api_keys_list",
     *     methods={"GET"}
     * )
     */
    public function keyList(KeysViewFactory $viewFactory, KeyRepositoryInterface $repository): KeysView
    {
        $key = $repository->getKeyByEmail($this->getUser()->getEmail());
        if (null === $key) {
            throw new NotFoundHttpException();
        }

        return $viewFactory->createSingle($key);
    }

    /**
     * Update keys.
     *
     * @SWG\Tag(name="Keys")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @Model(type=SaveKeysType::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Success keys update",
     *     @Model(type=KeysView::class)
     * )
     *
     * @Route(
     *     path="",
     *     name="api_keys_save",
     *     methods={"POST"}
     * )
     */
    public function saveKeys(
        Request $request,
        KeysViewFactory $viewFactory,
        KeysModifier $modifier,
        CaesarServerRpcInterface $caesarServerRpc
    ): KeysView {
        $keysRequest = new KeysRequest(
            $this->getUser()->getEmail(),
            $this->getUser()->getId(),
        );

        $form = $this->createForm(SaveKeysType::class, $keysRequest);
        $form->submit($request->request->all(), false);
        if ($form->isSubmitted() && !$form->isValid()) {
            throw new FormInvalidRequestException($form);
        }

        $caesarServerRpc->changedUserKeys($keysRequest->getUserId());
        $key = $modifier->createOrUpdateByRequest($keysRequest);

        return $viewFactory->createSingle($key);
    }

    /**
     * Get user public key.
     *
     * @SWG\Tag(name="Keys")
     * @SWG\Response(
     *     response=200,
     *     description="User public key",
     *     @Model(type=PublicKeyView::class)
     * )
     *
     * @Route(
     *     path="/{email}",
     *     name="api_get_public_key",
     *     methods={"GET"}
     * )
     */
    public function publicKey(
        string $email,
        PublicKeyViewFactory $factory,
        KeyRepositoryInterface $repository
    ): PublicKeyView {
        $key = $repository->getKeyByEmail($email);
        if (null === $key) {
            throw new NotFoundHttpException('Not found key by email');
        }

        return $factory->createSingle($key);
    }

    /**
     * Get users public keys by emails.
     *
     * @SWG\Tag(name="Keys")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @Model(type=PublicKeysType::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="User public key",
     *     @SWG\Schema(type="array", @Model(type=PublicKeyView::class))
     * )
     *
     * @Route(
     *     path="/batch",
     *     methods={"POST"}
     * )
     *
     * @return PublicKeyView[]
     */
    public function batchPublicKeys(
        Request $request,
        PublicKeyViewFactory $factory,
        KeyRepositoryInterface $repository
    ): array {
        $keysRequest = new PublicKeysRequest();
        $form = $this->createForm(PublicKeysType::class, $keysRequest);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            throw new FormInvalidRequestException($form);
        }

        return $factory->createCollection(
            $repository->getKeysByEmails($keysRequest->getEmails())
        );
    }

    /**
     * Update keys for user without keys.
     *
     * @SWG\Tag(name="Keys")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @Model(type=SaveKeysType::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Success keys update",
     *     @Model(type=KeysView::class)
     * )
     *
     * @Route(
     *     path="/{email}",
     *     name="api_user_update_keys",
     *     methods={"PATCH"}
     * )
     */
    public function patchKeys(
        string $email,
        Request $request,
        KeysViewFactory $viewFactory,
        KeysModifier $modifier,
        CaesarServerRpcInterface $caesarServerRpc
    ): KeysView {
        $keysRequest = new KeysRequest($email);

        $form = $this->createForm(SaveKeysType::class, $keysRequest);
        $form->submit($request->request->all(), false);
        if (!$form->isValid()) {
            throw new FormInvalidRequestException($form);
        }

        try {
            $caesarServerRpc->updatedUserKeys($keysRequest->getUserId());
            $key = $modifier->createByRequest($keysRequest);
        } catch (\InvalidArgumentException  $exception) {
            throw new AccessDeniedException();
        }

        return $viewFactory->createSingle($key);
    }
}
