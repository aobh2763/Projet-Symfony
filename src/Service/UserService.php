<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserService {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ManagerRegistry $doctrine
    ){
    }

    public function updatePersonalInfo(User $user, array $data): array {
        foreach (['firstName', 'lastName', 'email', 'username'] as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => 'All fields are required'];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return ['success' => true, 'message' => 'Your personal information has been updated successfully!'];
    }

    public function updateCart($cart, $quantities, $orderIds, $orderRepository, $entityManager): array {
        $errors = [];

        foreach ($orderIds as $orderId) {
            $quantity = isset($quantities[$orderId]) ? (int) $quantities[$orderId] : null;

            if (!is_numeric($quantity) || $quantity < 1 || $quantity > 100) {
                $errors[] = "Invalid quantity for order {$orderId}.";
                continue;
            }

            $order = $orderRepository->find($orderId);
            if (!$order) {
                $errors[] = "Order not found: {$orderId}.";
                continue;
            }

            if (!$cart->getOrders()->contains($order)) {
                $errors[] = "Order does not belong to your cart: {$orderId}.";
                continue;
            }

            $order->setQuantity($quantity);
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $cart->updatePrixTotal();

        try {
            $entityManager->persist($cart);
            $entityManager->flush();
            return ['success' => true, 'errors' => []];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update cart: ' . $e->getMessage()]];
        }
    }

    public function processPayment(
        $cart,
        float $shippingPrice,
        string $paymentMethod,
        HttpClientInterface $httpClient,
        string $flouciApiKey,
        string $flouciTrackingId,
        bool $isFlouciTestMode,
        string $edinarApiKey,
        string $edinarMerchantId
    ): array {
        $cart->updatePrixTotal();
        $totalAmount = $cart->getPrixTotal() + $shippingPrice + ($cart->getPrixTotal() * 0.01);

        if ($paymentMethod === 'flouci') {
            $apiUrl = $isFlouciTestMode
                ? 'https://sandbox.flouci.com/v1/payments'
                : 'https://api.flouci.com/v1/payments';

            $response = $httpClient->request('POST', $apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $flouciApiKey,
                    'Content-Type' => 'application/json',
                    'X-Developer-Tracking-Id' => $flouciTrackingId,
                ],
                'json' => [
                    'amount' => $totalAmount * 1000,
                    'currency' => 'TND',
                    'order_id' => 'FLOUCI_' . uniqid(),
                    'success_url' => 'http://localhost:8000/user/payment/success',
                    'cancel_url' => 'http://localhost:8000/user/payment/cancel',
                    'callback_url' => 'http://localhost:8000/user/payment/callback',
                ],
            ]);
            $data = $response->toArray();
            if (isset($data['payment_url'])) {
                return [
                    'success' => true,
                    'payment_url' => $data['payment_url'],
                    'payment_id' => $data['payment_id'],
                    'method' => 'flouci'
                ];
            }
            return ['success' => false, 'error' => 'Failed to create Flouci payment'];
        } elseif ($paymentMethod === 'edinar') {
            $apiUrl = 'https://api.gpgcheckout.com/v1/payments';
            $response = $httpClient->request('POST', $apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $edinarApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'merchant_id' => $edinarMerchantId,
                    'amount' => $totalAmount * 1000,
                    'currency' => 'TND',
                    'order_id' => 'EDINAR_' . uniqid(),
                    'success_url' => 'http://localhost:8000/user/payment/success',
                    'cancel_url' => 'http://localhost:8000/user/payment/cancel',
                    'callback_url' => 'http://localhost:8000/user/payment/callback',
                    'payment_method' => 'edinar',
                ],
            ]);
            $data = $response->toArray();
            if (isset($data['payment_url'])) {
                return [
                    'success' => true,
                    'payment_url' => $data['payment_url'],
                    'payment_id' => $data['payment_id'],
                    'method' => 'edinar'
                ];
            }
            return ['success' => false, 'error' => 'Failed to create e-Dinar payment'];
        }

        return ['success' => false, 'error' => 'Invalid payment method selected.'];
    }
}