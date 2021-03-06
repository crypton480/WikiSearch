<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Favorite;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateURL('searchPage'));
    }

	/**
	 * @Route("/search", name="searchPage")
	 */
	public function searchAction(Request $request)
	{
        $em = $this->getDoctrine()->getManager();

		$fav = new Favorite();

		$form = $this->createFormBuilder($fav)
                    ->add('title', 'text')
					->getForm();

		$form->handleRequest($request);

		$entries = array();
        $error = "";

        if(isset($_POST['sroffset']))
            $sroffset = $_POST['sroffset'];
        else
            $sroffset = 0;

        if($form->isValid())
        {
			$title = str_replace(" ", "%20", $form['title']->getData());

            if(isset($_POST['next']))
            {
                $sroffset+=10;
            }
            elseif(isset($_POST['prev']))
            {
                $sroffset = max(0, $sroffset-=10);
            }
			
			$response = $this->getJSON($title, $sroffset);

            if(isset($response['error']))
            {
                $error = "Wikipedia search is currently not available. Please try again later.";
            }
            else
            {
                $count = 0;
			    foreach($response['query']['search'] as $row)
			    {
                    
				    $entries[$count]['title'] = $row['title'];
                    $entries[$count]['snippet'] = str_replace("&quot;" , "\"", strip_tags($row['snippet']))."...";
                    $entries[$count]['link'] = "https://en.wikipedia.org/wiki/".str_replace(" ","_",$row['title']);
                    $entries[$count]['raw'] = str_replace(" ","_",$row['title']);

                    $isFav = $em->getRepository('AppBundle:Favorite');
                    if($isFav->findOneByTitle(str_replace(" ","_",$row['title'])))
                        $entries[$count]['isFav'] = 1;
                    else
                        $entries[$count]['isFav'] = 0;
                    
                    ++$count;
			    }
            }
			
		}

		return $this->render('AppBundle:mainApp:index.html.twig', array(
			'form' =>$form->createView(),
			'entries' => $entries,
            'error' => $error,
            'sroffset' => $sroffset
		));

		
	}

    /**
	 * @Route("/setFavorite/{title}", name="favorite")
	 */
    public function setFavoriteAction($title)
    {
        $em = $this->getDoctrine()->getManager();
        $isFav = $em->getRepository('AppBundle:Favorite')->findOneByTitle($title);
        if($isFav)
        {
            $em->remove($isFav);
            $em->flush();
            return new Response('Removed');
        }
        else
        {
            $fav = new Favorite();
            $fav->setTitle($title);
            $em = $this->getDoctrine()->getManager();
            $em->persist($fav);
            $em->flush();
            return new Response('Added');
        }

    }

    public function allFavoritesAction()
    {
        $favData = $this->getDoctrine()->getRepository('AppBundle:Favorite')->findAll();
        $favList = array();
        $count = 0;
        foreach($favData as $entry)
        {
            $favList[$count]['raw'] = $entry->getTitle();
            $favList[$count]['title'] = str_replace("_"," ",$entry->getTitle());
            ++$count;
        }

        return $this->render('AppBundle:mainApp:allfavs.html.twig', array(
            'favList'=> $favList
        ));
    }
    

    private function getJSON($title, $sroffset)
    {
        $searchURL = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=$title&format=json&sroffset=$sroffset";

		$cURL = curl_init($searchURL);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true); 
		$response = json_decode(curl_exec($cURL), true);
        return $response;

    }
}
