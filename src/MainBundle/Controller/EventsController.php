<?php

namespace MainBundle\Controller;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use MainBundle\Form\EventsType;
use MainBundle\Form\RechercheType;
use MainBundle\Entity\Events;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Event controller.
 *
 */
class EventsController extends Controller
{

    /**
     * Lists all event entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('MainBundle:Events')->findAll();


        return $this->render('events/index.html.twig', array(
            'events' => $events,

        ));
    }

    /**
     * Creates a new event entity.
     *
     */
    public function newAction(Request $request)
    {
        $event = new Events();
        $form = $this->createForm('MainBundle\Form\EventsType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('events_show', array('idEv' => $event->getIdev()));
        }

        return $this->render('events/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     */
    public function showAction(Events $event)
    {
        $deleteForm = $this->createDeleteForm($event);

        return $this->render('events/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     *
     */
    public function editAction(Request $request, Events $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('MainBundle\Form\EventsType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('events_show', array('idEv' => $event->getIdev()));
        }

        return $this->render('events/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     *
     */
    public function deleteAction(Request $request, Events $event)
    {
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('events_index');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Events $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Events $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('events_delete', array('idEv' => $event->getIdev())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    public function searchAction(Request $request)
    {
        $event = new Events();
        $form = $this->createForm(RechercheType::class, $event);
        $form = $form->handleRequest($request);
        $nom = $request->get('search') ;
        if ($form->isSubmitted()) {
            $em= $this->getDoctrine()->getManager()->getRepository(Events::class);
            $event= $em->find($event->getIdEv());

        }
        else
        {
            $event = $this->getDoctrine()->getRepository(Events::class)->findAll();
        }
        return $this->render('events/search.html.twig', array("form"=> $form->createView(),"events" => $event));

    }
    public function basebackAction()
    {
        return $this->render("baseback.html.twig");

    }

    public function accepterAction($idEv, Request $request)
    {


        $em = $this->getDoctrine()->getManager();
        $event = $this->getDoctrine()->getRepository(Events::class)->find($idEv);
        $event->setEtat("Accepter");
        $em->persist($event);
        $em->flush();
        return $this->redirectToRoute('events_index', array('idEv' => $event->getIdev()));
}
    public function refuserAction($idEv, Request $request)
    {


        $em = $this->getDoctrine()->getManager();
        $event = $this->getDoctrine()->getRepository(Events::class)->find($idEv);
        $event->setEtat("refuser");
        $em->persist($event);
        $em->flush();
        return $this->redirectToRoute('events_index', array('idEv' => $event->getIdev()));
    }
    public function SMSAction()
    {
        $account_sid = 'AC564d26deab05c8684882d7128e79a76a';
        $auth_token = '44d7b0630152762d627e78d613ff2b5a';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

// A Twilio number you own with SMS capabilities
        $twilio_number = "+18046813017";

        $client = new Client($account_sid, $auth_token);

            $client->messages->create(
            // Where to send a text message (your cell phone?)
                '+21629288735',
                array(
                    'from' => $twilio_number,
                    'body' => 'I sent this message in under 10 minutes!'
                )
            );



        return $this->redirectToRoute('events_index');

    }

}
