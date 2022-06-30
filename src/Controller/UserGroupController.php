<?php

namespace App\Controller;

use App\Entity\UserGroup;
use App\Form\UserGroupType;
use App\Repository\ServerRepository;
use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserGroupController extends AbstractController
{
    /**
     * @Route("/user/group/create", name="app_user_group_create")
     */
    public function createUserGroup(Request $request, ManagerRegistry $doctrine, ServerRepository $serverRepository, UserRepository $userRepository): Response
    {

        $users = $userRepository->findAllButCurrent($this->getUser()->getEmail());

        if (in_array("ROLE_SUPER_ADMIN", $this->getUser()->getRoles())) {
            $servers = $serverRepository->findAll();
        } else {
            $servers = $serverRepository->findBy(["createdBy" => $this->getUser()->getId()]);
        }

        $userGroup = new UserGroup();
        $form = $this->createForm(UserGroupType::class, $userGroup, ["servers" => $servers, "users" => $users]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($userGroup);
            $em->flush();
        }

        return $this->render('user_group/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/user/group/{id}/edit", name="app_user_group_edit")
     */
    public function editUserGroup($id, Request $request, ManagerRegistry $doctrine, UserGroupRepository $userGroupRepository, ServerRepository $serverRepository): Response
    {
        $userGroup = $userGroupRepository->find($id);

        if (in_array("ROLE_SUPER_ADMIN", $this->getUser()->getRoles())) {
            $servers = $serverRepository->findAll();
        } else {
            $servers = $serverRepository->findBy(["createdBy" => $this->getUser()->getId()]);
        }

        $form = $this->createForm(UserGroupType::class, $userGroup, ["servers" => $servers]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($userGroup);
            $em->flush();

            $this->addFlash("success", "Groupe modifié !");
            return $this->redirectToRoute("app_user_group_read", ["id" => $id]);
        }

        return $this->render('user_group/edit.html.twig', [
            "form" => $form->createView(),
            "group" => $userGroup
        ]);
    }

    /**
     * @Route("/user/group/{id}", name="app_user_group_read")
     */
    public function userGroup($id, UserGroupRepository $userGroupRepository)
    {
        $userGroup = $userGroupRepository->find($id);

        return $this->render("user_group/read.html.twig", [
            "group" => $userGroup
        ]);
    }
}
