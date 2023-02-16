<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Form\UserGroupType;
use App\Form\UserGroupServerType;
use App\Repository\UserRepository;
use App\Repository\ServerRepository;
use App\Repository\UserGroupRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserGroupController extends AbstractController
{
    /**
     * @Route("/user/group/create", name="app_user_group_create")
     */
    public function createUserGroup(Request $request, ManagerRegistry $doctrine, ServerRepository $serverRepository, UserRepository $userRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        $users = $userRepository->findAllButCurrent($user->getEmail());

        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            $servers = $serverRepository->findAll();
        } else {
            $servers = $serverRepository->findBy(["createdBy" => $user->getId()]);
        }

        $userGroup = new UserGroup();
        $form = $this->createForm(UserGroupType::class, $userGroup, ["servers" => $servers, "users" => $users]);
        $form->handleRequest($request);
        $userGroup->addUser($user);
        $userGroup->setCreatedBy($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($userGroup);
            $em->flush();

            $this->addFlash("success", "Groupe " . $userGroup->getName() . " ajouté !");
            return $this->redirectToRoute("app_user_group_read", ["id" => $userGroup->getId()]);
        }

        return $this->render('user_group/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/user/group/{id}/edit", name="app_user_group_edit")
     */
    public function editUserGroup($id, Request $request, ManagerRegistry $doctrine, UserGroupRepository $userGroupRepository, ServerRepository $serverRepository, UserRepository $userRepository): Response
    {
        $userGroup = $userGroupRepository->find($id);
        /** @var User */
        $user = $this->getUser();

        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            $servers = $serverRepository->findAll();
        } else {
            $servers = $serverRepository->findBy(["createdBy" => $user->getId()]);
        }

        $users = $userRepository->findAllButCurrent($user->getEmail());

        $form = $this->createForm(UserGroupType::class, $userGroup, ["servers" => $servers, "users" => $users]);
        $form->handleRequest($request);
        $userGroup->addUser($user);

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

    /**
     * @Route("/user/group/{id}/add/server", name="app_user_group_add_server")
     */
    public function userGroupAddServer($id, UserGroupRepository $userGroupRepository, Request $request, ServerRepository $serverRepository)
    {
        $userGroup = $userGroupRepository->find($id);

        /** @var User */
        $user = $this->getUser();

        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            $servers = $serverRepository->findAll();
        } else {
            $servers = $serverRepository->findBy(["createdBy" => $user->getId()]);
        }

        $form = $this->createForm(UserGroupServerType::class, null, ["servers" => $servers]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $servers = $form->getData()["servers"];
            dd($servers);
        }

        return $this->render("user_group/add-server.html.twig", [
            "form" => $form->createView(),
            "group" => $userGroup
        ]);
    }
}
