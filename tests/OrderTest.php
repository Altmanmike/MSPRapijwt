<?php

    use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Order;
    use App\Entity\Product;
    use App\Entity\User;

    class OrderTest extends KernelTestCase
    {
        private EntityManagerInterface $entityManager;

        protected function setUp(): void
        {
            parent::setUp();
            self::bootKernel();
            $this->entityManager = self::$container->get(EntityManagerInterface::class);
        }

        public function testCreateOrder()
        {
            $order = new Order();
            $order->setNomClient('John');
            $order->setPrenomClient('Doe');
            $order->setAdresseClient('123 Rue de l\'Exemple');
            $order->setTelClient('0123456789');

            $product1 = new Product();
            $product1->setNom('Produit 1');
            $product1->setDescription('Description du produit 1');
            $product1->setPrix(9.99);
            $product1->setQteStock(10);
            $product1->setImage('chemin/vers/image1.jpg');

            $product2 = new Product();
            $product2->setNom('Produit 2');
            $product2->setDescription('Description du produit 2');
            $product2->setPrix(19.99);
            $product2->setQteStock(5);
            $product2->setImage('chemin/vers/image2.jpg');

            $order->setProduits([$product1->getId(),$product2->getId()]);

            $entityManager = self::$container->get('doctrine')->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            $this->assertNotNull($order->getId());
            $this->assertContains($product1, $order->getProduits());
            $this->assertContains($product2, $order->getProduits());
        }

        public function testUpdateOrder()
        {
            self::bootKernel();

            $entityManager = self::$container->get('doctrine')->getManager();

            $order = new Order();
            $order->setNomClient('Sparrow');
            $order->setPrenomClient('Jack');
            $order->setAdresseClient('Quai du port');
            $order->setTelClient('0845123568');

            $product = new Product();
            $product->setNom('Produit 3');
            $product->setDescription('Description du produit 3');
            $product->setPrix(6.49);
            $product->setQteStock(2);
            $product->setImage('chemin/vers/image3.jpg');

            $order->setProduits([$product->getId()]);

            $entityManager->persist($order);
            $entityManager->flush();

            // Update the order
            $order->setNomClient('Jane');
            $order->setPrenomClient('Smith');

            $entityManager->flush();

            $updatedOrder = $entityManager->getRepository(Order::class)->find($order->getId());

            $this->assertEquals('Jane', $updatedOrder->getNomClient());
            $this->assertEquals('Smith', $updatedOrder->getPrenomClient());
        }

        public function testDeleteOrder()
        {
            self::bootKernel();

            $entityManager = self::$container->get('doctrine')->getManager();

            $order = new Order();
            $order->setNomClient('Presley');
            $order->setPrenomClient('Elvis');
            $order->setAdresseClient('1 rue du rock');
            $order->setTelClient('0123456789');

            $product = new Product();
            $product->setNom('Produit 4');
            $product->setDescription('Description du produit 4');
            $product->setPrix(45.00);
            $product->setQteStock(7);
            $product->setImage('chemin/vers/image4.jpg');

            $order->setProduits([$product->getId()]);

            $entityManager->persist($order);
            $entityManager->flush();

            $entityManager->remove($order);
            $entityManager->flush();

            $deletedOrder = $entityManager->getRepository(Order::class)->find($order->getId());

            $this->assertNull($deletedOrder);
        }

        public function testGetOrderProducts()
        {
            self::bootKernel();

            $entityManager = self::$container->get('doctrine')->getManager();

            $order = new Order();
            $order->setNomClient('Gates');
            $order->setPrenomClient('Bill');
            $order->setAdresseClient('Boulevard du chipset');
            $order->setTelClient('0512457889');

            $product1 = new Product();
            $product1->setNom('Produit 5');
            $product1->setDescription('Description du produit 5');
            $product1->setPrix(1.29);
            $product1->setQteStock(1);
            $product1->setImage('chemin/vers/image5.jpg');

            $product2 = new Product();
            $product2->setNom('Produit 6');
            $product2->setDescription('Description du produit 6');
            $product2->setPrix(33.99);
            $product2->setQteStock(4);
            $product2->setImage('chemin/vers/image6.jpg');

            $order->setProduits([$product1->getId(),$product2->getId()]);

            $entityManager->persist($order);
            $entityManager->flush();

            $orderProducts = $order->getProduits();

            $this->assertCount(2, $orderProducts);
            $this->assertContains($product1, $orderProducts);
            $this->assertContains($product2, $orderProducts);
        }
    }
