<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Court;
use App\Entity\Category;

class CourtController extends AbstractController
{
    /**
     * @Route("/api/courts", name="api_court_list")
     */
    public function listCourtsAPI()
    {
        $repository = $this->getDoctrine()
            ->getRepository(Court::class);
        $courts = $repository->findAll();
		$responseData = array();

		foreach ($courts as $court) {
            $categories = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findByCourtId($court->getId());

			$responseData[] = array(
				'type' => 'Feature',
				'properties' => array(
					'id' => $court->getId(),
					'name' => $court->getName(),
					'label' => $court->getLabel(),
                    'categories' => $categories,
				),
				'geometry' => array(
					'type' => 'Point',
					'coordinates' => [
						$court->getLng(),
						$court->getLat()
					]
				),
			);
		}

        return $this->json($responseData);
    }

    /**
     * @Route("/api/courts/{id}", name="court_show")
     */
    public function courtDetailAPI($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Court::class);
        $court = $repository->find($id);
		$responseData = array();

        if ( ! $court ) { return $this->json($responseData); }

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findByCourtId($court->getId());

        $responseData[] = array(
            'type' => 'Feature',
            'properties' => array(
                'id' => $court->getId(),
                'name' => $court->getName(),
                'label' => $court->getLabel(),
                'categories' => $categories,
            ),
            'geometry' => array(
                'type' => 'Point',
                'coordinates' => [
                    $court->getLng(),
                    $court->getLat()
                ]
            ),
        );

        return $this->json($responseData);
    }
    /**
     * @Route("/map", name="map")
     * @Route("/")
     */
    public function showInMap()
    {
        return $this->render('map.html.twig');
    }
}
