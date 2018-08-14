<?php
/**
 * Created by PhpStorm.
 * User: eschlegel
 * Date: 14/08/2018
 * Time: 17:15
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

/**
 * User controller.
 *
 * @Route("/")
 */
class UsersController extends Controller
{
    /**
     * Lists all user.
     * @FOSRest\Get("/users")
     * @QueryParam(name="offset", requirements="\d+", default="", description="pagination start index")
     * @QueryParam(name="limit", requirements="\d+", default="", description="*pagination end index")
     * @QueryParam(name="sort", requirements="(asc|desc)", nullable=true, description="sort order (based on id)")
     * @QueryParam(name="filter", nullable=true, description="filter on name")
     *
     * @return JsonResponse
     */
    public function getUsersAction(Request $request, ParamFetcher $paramFetcher)
    {
        // query for all user
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');
        $filter = $paramFetcher->get('filter');
        $users = $this->getDoctrine()->getRepository('App:User')->findAllWithLimit($offset, $limit, $sort, $filter);

        return new JsonResponse($users, Response::HTTP_OK , []);
    }


    /**
     * get one user by id
     * @FOSRest\Get("/user/id/{id}")
     *
     * @return JsonResponse
     */
    public function getUserByIdAction(String $id)
    {
        // query for all user
        $user = $this->getDoctrine()->getRepository('App:User')->findOneById($id);

        return new JsonResponse($user, Response::HTTP_OK , []);
    }
}