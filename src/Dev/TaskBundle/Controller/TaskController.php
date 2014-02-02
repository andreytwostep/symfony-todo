<?php

namespace Dev\TaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Dev\TaskBundle\Entity\Task;
use Dev\TaskBundle\Form\TaskType;
use Dev\TaskBundle\Form\TaskCompleteType;

/**
 * Task controller.
 *
 * @Route("/task")
 */
class TaskController extends Controller
{

    /**
     * Lists all Task entities.
     *
     * @Route("/", name="task")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('DevTaskBundle:Task')->findAll();
		$repository = $this->getDoctrine()->getRepository('DevTaskBundle:Task');
		$query = $repository->createQueryBuilder('t')
			->select('t.id', 't.task', 't.complete', 't.created')
			->orderBy('t.created', 'DESC')
			->getQuery();

		$entities = $query->getResult();

		$entity = new Task();
		$form   = $this->createCreateForm($entity);

        return array(
            'entities' => $entities,
			'entity' => $entity,
			'form'   => $form->createView(),
        );
    }
    /**
     * Creates a new Task entity.
     *
     * @Route("/", name="task_create")
     * @Method("POST")
     * @Template("DevTaskBundle:Task:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Task();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

			if($request->isXmlHttpRequest()) {
				$json = json_encode(array(
						'id' => $entity->getId(),
						'task' => $entity->getTask(),
				));

				$response = new Response($json);
				$response->headers->set('Content-Type', 'application/json');

				return $response;
			}
            return $this->redirect($this->generateUrl('task', array('id' => 81)));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Task entity.
    *
    * @param Task $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Task $entity)
    {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('task_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/new", name="task_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Task();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Task entity.
     *
     * @Route("/{id}", name="task_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DevTaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DevTaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


	/**
	 * Edits an existing Task entity.
	 *
	 * @Route("/{id}", name="task_update")
	 * @Method("PUT")
	 * @Template("DevTaskBundle:Task:edit.html.twig")
	 */
	public function updateAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('DevTaskBundle:Task')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Task entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		if ($editForm->isValid()) {
			$em->flush();

			if($request->isXmlHttpRequest()) {
				$json = json_encode(array(
						'id' => $entity->getId(),
						'task' => $entity->getTask(),
						'complete' => $entity->getComplete(),
					));

				$response = new Response($json);
				$response->headers->set('Content-Type', 'application/json');

				return $response;
			}

			return $this->redirect($this->generateUrl('task'));
		}

		return array(
			'entity'      => $entity,
			'edit_form'   => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		);
	}

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}", name="task_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
			if($request->isXmlHttpRequest()) {
				$json = json_encode(array(
					'id' => $id,
				));

				$repository = $this->getDoctrine()->getRepository('DevTaskBundle:Task');
				$query = $repository->createQueryBuilder('t')
					->delete()
					->where('t.id IN ('.$id.')')
					->getQuery();

				$query->getResult();

				$response = new Response($json);
				$response->headers->set('Content-Type', 'application/json');

				return $response;
			}
			//$this->get('session')->getFlashBag()->add('notice', 'Task deleted..');
        }

        return $this->redirect($this->generateUrl('task'));
    }

    /**
     * Creates a form to delete a Task entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('task_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

	/**
	 * Creates a form to edit a Task entity.
	 *
	 * @param Task $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(Task $entity)
	{
		$form = $this->createForm(new TaskType(), $entity, array(
				'action' => $this->generateUrl('task_update', array('id' => $entity->getId())),
				'method' => 'PUT',
			));

		$form->add('submit', 'submit', array('label' => 'Update'));

		return $form;
	}

}

