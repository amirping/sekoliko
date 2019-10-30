<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>.
 **/

namespace App\Controller\User;

use App\Constant\MessageConstant;
use App\Controller\AbstractBaseController;
use App\Entity\AdministrationType;
use App\Entity\Administrator;
use App\Form\AdministrationTypeType;
use App\Repository\AdministrationTypeRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdministrationTypeController.
 *
 * @Route("admin/administration/type")
 */
class AdministrationTypeController extends AbstractBaseController
{
    /**
     * @Route("/list",name="administration_type_list",methods={"POST","GET"})
     *
     * @param AdministrationTypeRepository $repository
     *
     * @return RedirectResponse|Response
     */
    public function list(AdministrationTypeRepository $repository)
    {
        return $this->render(
            'admin/content/user/administration_type_list.html.twig',
            [
                'types' => $repository->findBy(['etsName' => $this->getUser()->getEtsName()]),
            ]
        );
    }

    /**
     * @Route("/manage/{id?}",name="administration_type_manage",methods={"POST","GET"})
     *
     * @param Request                 $request
     * @param AdministrationType|null $administrationType
     *
     * @return RedirectResponse|Response
     */
    public function new(Request $request, AdministrationType $administrationType = null)
    {
        $admin = $administrationType ?: new AdministrationType();
        $form = $this->createForm(AdministrationTypeType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (true === $this->em->save($admin, $this->getUser())) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::AJOUT_MESSAGE);

                return $this->redirectToRoute('administration_type_list');
            } else {
                $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);

                return $this->redirectToRoute('administration_type_manage', ['id' => $administrationType->getId() ?? null,]);
            }
        }

        return $this->render(
            'admin/content/user/_administration_type.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}",name="administration_type_delete",methods={"POST","GET"})
     *
     * @param AdministrationType $administrationType
     *
     * @return RedirectResponse
     */
    public function delete(AdministrationType $administrationType)
    {
        $repos = $this->manager->getRepository(Administrator::class)->findBy(['type' => $administrationType]);
        if (null === $repos) {
            if (true === $this->em->remove($administrationType)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, MessageConstant::SUPPRESSION_MESSAGE);
            } else {
                $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_MESSAGE);
            }
        } else {
            $this->addFlash(MessageConstant::ERROR_TYPE, MessageConstant::ERROR_ASSOCIATION_MESSAGE);
        }

        return $this->redirectToRoute('administration_type_list');
    }
}
