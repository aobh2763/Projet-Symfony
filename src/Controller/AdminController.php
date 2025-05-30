<?php

namespace App\Controller;

use App\Entity\{Gun, Accessory, Ammo, Melee};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{Response, RequestStack};

use App\Entity\Product;
use App\Service\{AdminService, MainService};
use App\Form\{
    OrdersFilterTypeForm, 
    FilterTypeForm, 
    CreateProductTypeForm, 
    CreateGunTypeForm, 
    CreateMeleeTypeForm,
    CreateAmmoTypeForm,
    CreateAccessoryTypeForm,
    PickProductTypeForm
};

final class AdminController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private AdminService $adminService,
        private MainService $mainService
    ){}

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_admin_orders');
    }

    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function getOrders(): Response
    {
        $user = $this->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm(OrdersFilterTypeForm::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $filters = $form->isSubmitted()
            ? $form->getData()
            : [];

        $orders = $this->adminService->getOrders($filters);

        return $this->render('admin/orders.html.twig', [
            'user' => $user,
            'filterform' => $form,
            'orders' => $orders
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function getUsers(): Response
    {
        $user = $this->getUser();
        $users = $this->adminService->getUsers();

        return $this->render('admin/users.html.twig', [
            'user' => $user,
            'users' => $users
        ]);
    }   
    
    #[Route('/admin/create-product', name: 'app_admin_create_product')]
    public function createProduct(): Response
    {
        $user = $this->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $pickTypeForm = $this->createForm(PickProductTypeForm::class);
        $pickTypeForm->handleRequest($request);

        $data = $pickTypeForm->getData() ?? ['product_type' => 'gun'];

        if($data['product_type'] === 'gun'){
            $product = new Gun();
            $form = $this->createForm(CreateGunTypeForm::class, $product);
        }
        if($data['product_type'] === 'ammo'){
            $product = new Ammo();
            $form = $this->createForm(CreateAmmoTypeForm::class, $product);
        }
        if($data['product_type'] === 'accessory'){
            $product = new Accessory();
            $form = $this->createForm(CreateAccessoryTypeForm::class, $product);
        }
        if($data['product_type'] === 'melee'){
            $product = new Melee();
            $form = $this->createForm(CreateMeleeTypeForm::class, $product);
        }

        if($form->isSubmitted() && $form->isValid()){
            $this->adminService->createProduct($product);
        }

        return $this->render('admin/create-product.html.twig', [
            'user' => $user,
            'pickTypeForm' => $pickTypeForm,
            'form' => $form
        ]);
    }   

    #[Route('/admin/products', name: 'app_admin_products')]
    public function getProducts(){
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm(FilterTypeForm::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        $filters = $form->isSubmitted() && $form->isValid()
            ? $form->getData()
            : [];

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $products = $this->mainService->getProducts($filters, $page, $limit);

        return $this->render('admin/products.html.twig', [
            'filters' => $filters,
            'filterform' => $form,
            'products' => $products
        ]);
    }

    #[Route('/admin/product-details/{id}', name: 'app_admin_product_details')]
    public function getProductDetails(int $id){
        $request = $this->requestStack->getCurrentRequest();

        $product = $this->mainService->getProduct($id);
        
        $form = null;
        if($product instanceof Gun){
            $form = $this->createForm(CreateGunTypeForm::class, $product);
        } elseif($product instanceof Ammo){
            $form = $this->createForm(CreateAmmoTypeForm::class, $product);
        } elseif($product instanceof Accessory){
            $form = $this->createForm(CreateAccessoryTypeForm::class, $product);
        } elseif($product instanceof Melee){
            $form = $this->createForm(CreateMeleeTypeForm::class, $product);
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->adminService->updateProduct($product);
        }

        return $this->render('admin/product-details.html.twig',[
            'product' => $product,
            'form' => $form
        ]);
    }

    #[Route('/admin/product-details/delete/{id}', 'app_admin_delete_product')]
    public function deleteProduct(int $id){
        $this->adminService->deleteProduct($id);
        return $this->redirectToRoute('app_admin_products');
    }
}
