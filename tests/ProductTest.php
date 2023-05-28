<?php

    use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Product;

    class ProductTest extends KernelTestCase
    {
        private EntityManagerInterface $entityManager;

        protected function setUp(): void
        {
            parent::setUp();
            self::bootKernel();
            $this->entityManager = self::$container->get(EntityManagerInterface::class);
        }

        public function testCreateProduct()
        {
            $product = new Product();
            $product->setNom('Produit 1');
            $product->setDescription('Description du produit 1');
            $product->setPrix(9.99);
            $product->setQteStock(10);
            $product->setImage('chemin/vers/image1.jpg');

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->assertNotNull($product->getId());
            $this->assertEquals('Produit 1', $product->getNom());
            $this->assertEquals('Description du produit 1', $product->getDescription());
            $this->assertEquals(9.99, $product->getPrix());
            $this->assertEquals(10, $product->getQteStock());
            $this->assertEquals('chemin/vers/image1.jpg', $product->getImage());
        }

        public function testUpdateProduct()
        {
            // Create an existing product
            $product = new Product();
            $product->setNom('Produit 2');
            $product->setDescription('Description du produit 2');
            $product->setPrix(19.99);
            $product->setQteStock(5);
            $product->setImage('chemin/vers/image2.jpg');

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            // Update the product
            $product->setNom('Produit 2 mis à jour');
            $product->setDescription('Nouvelle description du produit 2');
            $product->setPrix(29.99);
            $product->setQteStock(3);
            $product->setImage('chemin/vers/image2_maj.jpg');

            $this->entityManager->flush();

            $updatedProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());

            $this->assertEquals('Produit 2 mis à jour', $updatedProduct->getNom());
            $this->assertEquals('Nouvelle description du produit 2', $updatedProduct->getDescription());
            $this->assertEquals(29.99, $updatedProduct->getPrix());
            $this->assertEquals(3, $updatedProduct->getQteStock());
            $this->assertEquals('chemin/vers/image2_maj.jpg', $updatedProduct->getImage());
        }

        public function testDeleteProduct()
        {
            $product = new Product();
            $product->setNom('Produit 3');
            $product->setDescription('Description du produit 3');
            $product->setPrix(35.99);
            $product->setQteStock(9);
            $product->setImage('chemin/vers/image3.jpg');

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $productId = $product->getId();

            $this->entityManager->remove($product);
            $this->entityManager->flush();

            $deletedProduct = $this->entityManager->getRepository(Product::class)->find($productId);

            $this->assertNull($deletedProduct);
        }
    }
