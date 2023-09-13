<?php

namespace App\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Tag;


class TagControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        // Wysyłamy żądanie GET do akcji index
        $client->request('GET', '/tag');

        // Sprawdzamy, czy odpowiedź jest sukcesem (kod HTTP 200)
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Sprawdzamy, czy treść odpowiedzi zawiera oczekiwane treści
        $this->assertSelectorTextContains('h1', 'Tagi');
        $this->assertSelectorExists('a:contains("Nowy rekord")');
    }

    public function testCreate()
    {
        $client = static::createClient();

        // Wysyłamy żądanie GET do akcji create
        $crawler = $client->request('GET', '/tag/create');

        // Sprawdzamy, czy odpowiedź jest sukcesem (kod HTTP 200)
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Sprawdzamy, czy treść odpowiedzi zawiera formularz
        $this->assertStringContainsString('form', $client->getResponse()->getContent());

        // Symulujemy wypełnienie formularza i złożenie go
        $form = $crawler->selectButton('Zapisz')->form();
        $form['tag[title]'] = 'Nowa nazwa tagu'; // Tutaj uzupełnij dane
        $client->submit($form);

        // Sprawdzamy, czy odpowiedź jest przekierowaniem (kod HTTP 302)
        $this->assertResponseRedirects('/tag'); // Upewnij się, że to jest prawidłowa ścieżka

        // Możesz także sprawdzić, czy po przekierowaniu znajdujesz się na odpowiedniej stronie
        $crawler = $client->followRedirect();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Tagi');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $tag = new Tag();
        $tag->setTitle('Tag do usunięcia');

        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->persist($tag);
        $em->flush();

        $crawler = $client->request('GET', '/tag/' . $tag->getId() . '/delete');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString('form', $client->getResponse()->getContent());

        $form = $crawler->selectButton('Usuń')->form();
        $client->submit($form);

        $this->assertResponseRedirects('/tag');
    }

    public function testEdit()
    {
        $client = static::createClient();

        $tag = new Tag();
        $tag->setTitle('Tag do edycji');

        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->persist($tag);
        $em->flush();

        $crawler = $client->request('GET', '/tag/' . $tag->getId() . '/edit');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString('form', $client->getResponse()->getContent());

        $form = $crawler->selectButton('Edytuj')->form();
        $form['tag[title]'] = 'Nowy tytuł tagu';
        $client->submit($form);

        $this->assertResponseRedirects('/tag');
    }

}
