<?php

namespace App\Controller;

//use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\LoyaltyReward;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin/guests')]
final class GuestUserApiController extends AbstractController
{
    #[Route('', name: 'api_admin_guest_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->createQueryBuilder('u')
            ->where('JSON_CONTAINS(u.roles, :role) = 1')
            ->setParameter('role', json_encode('ROLE_GUEST'))
            ->getQuery()
            ->getResult();

        $data = [];

        foreach ($users as $user) {
            $rewardsData = [];

            foreach ($user->getLoyaltyRewards() as $reward) {
                $rewardsData[] = [
                    'id' => $reward->getId(),
                    'reward' => $reward->getReward(), // assuming getReward() returns an array
                ];
            }

            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'loyaltyRewards' => $rewardsData,
            ];
        }

        return $this->json($data, 200, [], ['groups' => 'guest:read']);
    }

    #[Route('/{id}', name: 'api_admin_guest_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        if (!in_array('ROLE_GUEST', $user->getRoles(), true)) {
            return $this->json(['error' => 'Not a guest user.'], 403);
        }

        $rewardsData = [];

        foreach ($user->getLoyaltyRewards() as $reward) {
            $rewardsData[] = [
                'id' => $reward->getId(),
                'reward' => $reward->getReward(), // assuming getReward() returns an array
            ];
        }

        $data[] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'loyaltyRewards' => $rewardsData,
        ];

        return $this->json($data, 200, [], ['groups' => 'guest:read']);
    }

    #[Route('', name: 'api_admin_guest_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->json(['error' => 'Email and password are required.'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_GUEST']);

        if (isset($data['rewards']) && is_array($data['rewards'])) {
            foreach ($data['rewards'] as $rewardId) {
                $reward = $em->getRepository(LoyaltyReward::class)->find($rewardId);
                if ($reward) {
                    $user->setLoyaltyRewards($reward);
                }
            }
        }

        $em->persist($user);
        $em->flush();

        return $this->json($user, 201, [], ['groups' => 'guest:read']);
    }

    #[Route('/{id}', name: 'api_admin_guest_update', methods: ['PUT'])]
    public function update(
        Request $request,
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        if (!in_array('ROLE_GUEST', $user->getRoles(), true)) {
            return $this->json(['error' => 'Not a guest user.'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (!empty($data['password'])) {
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        }

        if (isset($data['rewards']) && is_array($data['rewards'])) {
            $user->getLoyaltyRewards()->clear();
            foreach ($data['rewards'] as $rewardId) {
                $reward = $em->getRepository(LoyaltyReward::class)->find($rewardId);
                if ($reward) {
                    $user->setLoyaltyRewards($reward);
                }
            }
        }

        $em->flush();

        return $this->json($user, 200, [], ['groups' => 'guest:read']);
    }

    #[Route('/{id}', name: 'api_admin_guest_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        if (!in_array('ROLE_GUEST', $user->getRoles(), true)) {
            return $this->json(['error' => 'Not a guest user.'], 403);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(null, 204);
    }
}
