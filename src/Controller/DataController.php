<?php

namespace App\Controller;

use PDO;
use PDOException;
use Faker\Factory;
use App\Entity\User;
use App\Service\ResetDataService;
use App\Service\FetchWithHttpResponseService;
//use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
//use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DataController extends AbstractController
{
    #[Route('/data', name: 'app_dataHome')]
    public function index(): Response
    {
        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',
        ]);
    }

    #[Route('/api', name: 'app_goAPI')]
    public function goAPI() {}

    #[Route('/data-reset', name: 'app_dataReset')]
    public function dataReset(ResetDataService $resetDataService): Response
    {
        $result = '';
        try {
            $resetDataService->resetTablesAndIncrements();            
            $result = "Les données de la base ont correctement été effacées et réinitialisées";
        } catch(\Exception $e) {
            $result = $e->getMessage();
        }        

        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',
            'result' => $result,
        ]);
    }

    #[Route('/data-load-users', name: 'app_dataLoadUsers')]
    public function dataLoadUsers(EntityManagerInterface $manager): Response
    {

        // Génération d'une DataFixtures de fausses données d'utilisateurs via FakerPHP
        $faker = Factory::create('fr_FR');

        for($i=0; $i < 50; $i++)
        {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',10)),0,25));       

            $manager->persist($user);        
        }

        $user = new User();
        $user->setEmail('test@test.fr');
        $user->setPassword('967520ae23e8ee14888bae72809031b98398ae4a636773e18fff917d77679334');       

        $manager->persist($user);
        
        $manager->flush(); 
        
        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',            
        ]);
    }

    #[Route('/data-reset-users', name: 'app_dataResetUsers')]
    public function dataResetUsers(ResetDataService $resetDataService): Response
    {
        $result = '';
        try {
            $resetDataService->resetUsersTablesAndIncrements();            
            $result = "Les données de la table Users ont correctement été effacées et réinitialisées";
        } catch(\Exception $e) {
            $result = $e->getMessage();
        }        

        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',
            'result' => $result,
        ]);
    }  
    
    /************************************************** */
    #[Route('/data/data-getJson', name: 'app_dataGetJson')]
    public function dataGetJson(FetchWithHttpResponseService $fetchWithHttpResponseService): Response
    {
        ini_set('max_execution_time', '500'); //360 seconds = 6 minutes
        set_time_limit(500);

        $url = 'https://615f5fb4f7254d0017068109.mockapi.io/api/v1/customers';
        
        $customers = $fetchWithHttpResponseService->getJsonFromAPI($url);

        $faker = Factory::create('fr_FR');

        // Récupérez les variables d'environnement pour la base de données
        $dbhost = $_SERVER['DB_HOST'];
        $dbname = $_SERVER['DB_NAME'];
        $dbUser = $_SERVER['DB_USER'];
        $dbPass = $_SERVER['DB_PASS'];
        
        // Connexion à la bdd:
        try {
            $pdo = new PDO("mysql:host=$dbhost; dbname=$dbname", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connexion MySQL local failed: " . $e->getMessage());
        }

        $sqlOrders = "INSERT INTO `order` (`id`, `date`, `produits`, `nom_client`, `prenom_client`, `adresse_client`, `tel_client`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sthO = $pdo->prepare($sqlOrders);

        $sqlProducts = "INSERT INTO `product` (`id`, `nom`, `description`, `prix`, `qte_stock`, `image`) VALUES (?, ?, ?, ?, ?, ?)";
        $sthP = $pdo->prepare($sqlProducts);

        foreach ($customers as $customer) {
            $dataDecodedOrders = $customer['orders'];
    
            foreach ($dataDecodedOrders as $dataDecodeOrder) {
                $idCustomer = $dataDecodeOrder['customerId'];
                $idOrder = $dataDecodeOrder['id'];
                $produits = (array) null;                

                // Récupération des produits des commandes du client
                $url = "https://615f5fb4f7254d0017068109.mockapi.io/api/v1/customers/$idCustomer/orders/$idOrder/products";
                
                $dataDecodedProducts = $fetchWithHttpResponseService->getJsonFromAPI($url);                    
                
                if($dataDecodedProducts !== null )
                {
                    foreach ($dataDecodedProducts as $dataDecodedProduct)
                    {                     
                        if(!empty($dataDecodedProduct['stock'])) {
                            array_push($produits, '{idProduit: '.$dataDecodedProduct['id'].', Qte: '.$dataDecodedProduct['stock'].'}');
                        } else {                            
                            array_push($produits, '{idProduit: '.$dataDecodedProduct['id'].', Qte: indéfini }');
                        }
        
                        $sthP->execute([ $dataDecodedProduct['id'], $dataDecodedProduct['name'], $dataDecodedProduct['details']['description'], $dataDecodedProduct['details']['price'], $faker->randomNumber(5, false),  $faker->url() ]);
                    } 
                }                                                     
    
                $sthO->execute([ $dataDecodeOrder['id'], $dataDecodeOrder['createdAt'], Json_encode($produits), $customer['lastName'], $customer['firstName'], $customer['address']['city'].', '.$customer['address']['postalCode'], $faker->mobileNumber() ]);
            }
        }   
        
        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',
        ]);
    }

    #[Route('/data-products', name: 'app_dataProducts')]
    public function dataProducts(ProductRepository $repoP): Response
    {
        $products = $repoP->findAll();

        return $this->render('data/products.html.twig', [
            'controller_name' => 'DataController',
            'products' => $products
        ]);
    }

    #[Route('/data-orders', name: 'app_dataOrders')]
    public function dataOrders(OrderRepository $repoO): Response
    {
        $orders = $repoO->findAll();

        return $this->render('data/orders.html.twig', [
            'controller_name' => 'DataController',
            'orders' => $orders
        ]);
    }

    #[Route('/data-users', name: 'app_dataUsers')]
    public function dataUsers(UserRepository $repoU): Response
    {
        $users = $repoU->findAll();

        return $this->render('data/users.html.twig', [
            'controller_name' => 'DataController',
            'users' => $users
        ]);
    }

    #[Route('/dataAuto', name: 'app_dataAuto')]
    public function dataAuto(): Response
    {

        return $this->render('data/index.html.twig', [
            'controller_name' => 'DataController',
        ]);
    }
}
