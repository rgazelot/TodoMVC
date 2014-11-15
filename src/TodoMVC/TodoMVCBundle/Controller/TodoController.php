<?php

namespace TodoMVC\TodoMVCBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use TodoMVC\TodoMVCBundle\Entity\Todo;

class TodoController extends Controller
{
    /**
     * @Route("/", name="list")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $todos = $em->getRepository('TodoMVCTodoMVCBundle:Todo')->all($request->query->get('sort', '-createdAt'), $request->query->get('filter', null));

        $checkForms  = [];
        $deleteForms = [];

        foreach ($todos as $todo) {
            $deleteForms[$todo->getId()] = $this->createFormBuilder()
                ->add('delete', 'submit')
                ->getForm()
                ->createView();

            $checkForms[$todo->getId()] = $this->createFormBuilder()
                ->add('check', 'submit')
                ->getForm()
                ->createView();
        }

        $clearForm = $this->createFormBuilder()
            ->add('clear', 'submit')
            ->getForm()
            ->createView();

        return $this->render('TodoMVCTodoMVCBundle:Todo:list.html.twig', [
            'todos'       => $todos,
            'checkForms'  => $checkForms,
            'deleteForms' => $deleteForms,
            'clearForm'   => $clearForm,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo;

        $form = $this->createFormBuilder($todo)
            ->add('title', 'text')
            ->add('create', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            return $this->redirect($this->generateUrl('list'));
        }

        return $this->render('TodoMVCTodoMVCBundle:Todo:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check/{id}", name="check")
     * @Method("PUT")
     */
    public function checkAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('TodoMVCTodoMVCBundle:Todo')->find($id);
        $todo->toggle();

        $em->persist($todo);
        $em->flush();

        return $this->redirect($this->generateUrl('list'));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($em->getRepository('TodoMVCTodoMVCBundle:Todo')->find($id));
        $em->flush();

        return $this->redirect($this->generateUrl('list'));
    }

    /**
     * @Route("/clear", name="clear")
     * @Method("DELETE")
     */
    public function clearAction()
    {
        $em = $this->getDoctrine()->getManager();
        $todos = $em->getRepository('TodoMVCTodoMVCBundle:Todo')->findBy(['completed' => true]);

        foreach ($todos as $todo) {
            $em->remove($todo);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('list'));
    }
}
