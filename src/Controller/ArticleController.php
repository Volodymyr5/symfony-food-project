<?php

namespace App\Controller;


use Michelf\MarkdownInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     * @return Response
     */
    public function homepage()
    {
        return $this->render('article/homepage.html.twig');
    }

    /**
     * @Route("/news/{slug}", name="article_show")
     * @param $slug
     * @param MarkdownInterface $markdown
     * @param AdapterInterface $cache
     * @return Response
     */
    public function show($slug, MarkdownInterface $markdown, AdapterInterface $cache)
    {
        $comments = [
            'I ate a normal rock once. It did NOT taste like bacon!',
            'Woohoo! I\'m going on an all-asteroid diet!',
            'I like bacon too! Buy some from my site! bakinsomebacon.com',
        ];

        $articleContent = <<<EOF
Spicy ***jalapeno bacon*** ipsum dolor amet veniam shank in dolore. Ham [hock](http://test.com) nisi landjaeger cow,lorem proident beef ribs aute enim veniam ut cillum pork chuck picanha. Dolore reprehenderitlabore minim pork belly spare ribs cupim short loin in. Elit exercitation eiusmod dolore cow **turkey** shank eu pork belly meatball non cupim.

Laboris beef ribs fatback fugiat eiusmod jowl kielbasa alcatra dolore velit ea ball tip. Pariaturlaboris sunt venison, et laborum dolore minim non meatball. Shankle eu flank aliqua shoulder,capicola biltong frankfurter boudin cupim officia. Exercitation fugiat consectetur ham. Adipisicingpicanha shank et filet mignon pork belly ut ullamco. Irure velit turducken ground round doner incididuntoccaecat lorem meatball prosciutto quis strip steak.

Meatball adipisicing ribeye bacon strip steak eu. Consectetur ham hock pork hamburger enim strip steakmollit quis officia meatloaf tri-tip swine. Cow ut reprehenderit, buffalo incididunt in filet mignonstrip steak pork belly aliquip capicola officia. Labore deserunt esse chicken lorem shoulder tail consecteturcow est ribeye adipisicing. Pig hamburger pork belly enim. Do porchetta minim capicola irure pancetta chuckfugiat.

Sausage tenderloin officia jerky nostrud. Laborum elit pastrami non, pig kevin buffalo minim ex quis. Pork bellypork chop officia anim. Irure tempor leberkas kevin adipisicing cupidatat qui buffalo ham aliqua pork bellyexercitation eiusmod. Exercitation incididunt rump laborum, t-bone short ribs buffalo ut shankle pork chopbresaola shoulder burgdoggen fugiat. Adipisicing nostrud chicken consequat beef ribs, quis filet mignon do.Prosciutto capicola mollit shankle aliquip do dolore hamburger brisket turducken eu.

Do mollit deserunt prosciutto laborum. Duis sint tongue quis nisi. Capicola qui beef ribs dolore pariatur.Minim strip steak fugiat nisi est, meatloaf pig aute. Swine rump turducken nulla sausage. Reprehenderit porkbelly tongue alcatra, shoulder excepteur in beef bresaola duis ham bacon eiusmod. Doner drumstick short loin,adipisicing cow cillum tenderloin.
EOF;

        $item = $cache->getItem('markdown_' . md5($articleContent));
        if (!$item->isHit()) {
            $item->set($markdown->transform($articleContent));
            $cache->save($item);
        }
        $articleContent = $item->get();

        return $this->render('article/show.html.twig', [
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'articleContent' => $articleContent,
            'slug' => $slug,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/news/{slug}/heart", name="article_toggle_heart", methods={"POST"})
     */
    public function toggleArticleHeart($slug, LoggerInterface $logger)
    {
        $logger->info('Article is being Hearted');

        // TODO - make heart / unhurt
        return new JsonResponse([
            'hearts' => rand(5, 100)
        ]);
    }
}