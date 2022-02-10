<?php


namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Initxlab\Ngd\Params\C;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{

    private const MSG_ERR_REQUEST_TYPE = 'Invalid request type : check that the Content-Type is "application/json".';
    private const MSG_LOGOUT_EXCEPTION = 'should not be reached';

    /**
     * @param IriConverterInterface $iriConverter
     * @return Response
     */
    #[Route(C::ROUTE_APP_LOGIN_PATH,name: C::ROUTE_APP_LOGIN_NAME,methods: [C::_POST])]
    public function login(IriConverterInterface $iriConverter): Response {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                C::KEY_ERROR => self::MSG_ERR_REQUEST_TYPE
            ],400);
        }

        return new Response(null,204,[
            C::HEADER_LOCATION => $iriConverter->getIriFromItem($this->getUser())
        ]);
    }

    /**
     * @throws
     */
    #[Route(C::ROUTE_APP_LOGOUT_PATH,name: C::ROUTE_APP_LOGOUT_NAME)]
    public function logout(): void
    {
        throw new RuntimeException(self::MSG_LOGOUT_EXCEPTION);
    }
}