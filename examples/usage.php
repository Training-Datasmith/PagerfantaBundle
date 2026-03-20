<?php

declare(strict_types=1);

/**
 * PagerfantaBundle — pagination in a Symfony controller example.
 *
 * Shows how to paginate a Doctrine ORM query and render pagination in Twig.
 *
 * --- Controller ---
 *
 * use Doctrine\ORM\EntityManagerInterface;
 * use Pagerfanta\Doctrine\ORM\QueryAdapter;
 * use Pagerfanta\Pagerfanta;
 * use Symfony\Component\HttpFoundation\Request;
 *
 * class ProductController extends AbstractController
 * {
 *     public function index(Request $request, EntityManagerInterface $em): Response
 *     {
 *         $qb = $em->getRepository(Product::class)->createQueryBuilder('p')
 *             ->orderBy('p.name', 'ASC');
 *
 *         $pagerfanta = new Pagerfanta(new QueryAdapter($qb));
 *         $pagerfanta->setMaxPerPage(20);
 *         $pagerfanta->setCurrentPage(max(1, $request->query->getInt('page', 1)));
 *
 *         return $this->render('product/index.html.twig', [
 *             'products' => $pagerfanta,
 *         ]);
 *     }
 * }
 *
 * --- Twig template (product/index.html.twig) ---
 *
 * {% for product in products %}
 *     <div>{{ product.name }}</div>
 * {% endfor %}
 *
 * {{ pagerfanta(products, 'twitter_bootstrap5') }}
 *
 * --- Customise the default view globally ---
 *
 * # config/packages/babdev_pagerfanta.yaml
 * babdev_pagerfanta:
 *     default_view: twitter_bootstrap5
 *
 * --- Automatic 404 on out-of-range pages ---
 *
 * The bundle registers an event listener that catches
 * NotValidCurrentPageException and converts it to a 404 response.
 * No extra code is needed in the controller.
 */

echo 'PagerfantaBundle requires a Symfony kernel.' . PHP_EOL;
echo 'See the docblock above for usage patterns.' . PHP_EOL;
