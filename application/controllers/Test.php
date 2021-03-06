<?php

class Test extends CI_Controller
{
    private $testNB = 0;
    private $testPassed = 0;
    private static $tables = array(
        'Auteur',
        'Classe',
        'Livre',
        'Rallye',
        'Theme',
        'Utilisateur'
    );

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Unit_test');
        $this->load->library('Formatter',null,'format');
        // DB Insulation
        //$this->db = $this->load->database('test', TRUE);

        $this->testNB = 0;
        $this->testPassed = 0;
    }

    public function index()
    {
        $data = array();
//        $this->resetAI();
//
//        $data['report']['user'] = $this->userTest();
//        $data['report']['livre'] = $this->livreTest();
//        $data['report']['emprunt'] = $this->empruntTest();

        $data['PassedTest'] = $this->testPassed;
        $data['NumberOfTest'] = $this->testNB;

        $this->load->view('test/display',$data);
        // Reset to prod db
        //$this->db = $this->load->database('default', TRUE);
    }

    public function resize()
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = '/home/guillaume/Projets4/assets/img/livres/test.jpeg';
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = FALSE;
        $config['width']     = BOOK_PIC_WIDTH;
        $config['height']   = BOOK_PIC_HEIGHT;
        $this->load->library('image_lib',$config);

//        $this->image_lib->clear();
//        $this->image_lib->initialize($config);

        if(!$this->image_lib->resize()){
            dump($this->image_lib);
            $this->image_lib->display_errors('<p>', '</p>');
        }
    }

    public function getImage()
    {
        if (isset($_GET['url'])){
            $img=file_get_contents($_GET['url']);
            file_put_contents(__DIR__.'/../../'.BOOK_PATH.'image',$img);
        }
    }

    private function livreTest()
    {
        // Declaration des resultat
        $result = array();

        // Variable récurrente de la fonction, ici l'id du livre tester tout au long
        $livre_id = '1';

        // Declaration des résultat attendu pour tous les tests
        // Attention, lors du ->result_array() des models CodeIgniter renvoie un tablea sous la form :
        // array([0]=>array(
        //      'super_nom_de_champ'=>'super_valeur'
        //  ))
        // Meme si une seul ligne correspond a la requete donnée
        $expected_get[0] = array(
            'id'=>'1',
            'isbn'=>null,
            'titre'=>'Harry Potter et la chambre des secrets',
            'auteur'=>'J.K. Rowling',
            'edition'=>'Folio Junior',
            'parution'=>'2017-10-12',
            'couverture'=>'assets/img/livres/1.jpg',
            'description'=>'',
            'disponible'=>'0'
        );

        // Pour les tests d'ajouts la fonction renvoie un boolean mais on dois tester las valeurs insérées
        // Donc, on est obligés de faire un get derriere et tester sur le get
        // /!\ Ca implique que, si un get foire, tout les test suivant foire !!
        $expected_add = array(
            'isbn'=>null,
            'titre'=>'Harry Potter',
            'auteur'=>'J.K. Rowling',
            'edition'=>'Folio Junior',
            'parution'=>'2017-10-12',
            'couverture'=>'assets/img/livres/2.jpg',
            'description'=>'',
            'disponible'=>'0'
        );

        // Idem que precedemment
        $expected_set = array(
            'isbn'=>null,
            'titre'=>'Harry Petteur',
            'auteur'=>'J.K. Rowling',
            'edition'=>'Folio Junior',
            'parution'=>'2017-10-12',
            'couverture'=>'assets/img/livres/2.jpg',
            'description'=>'',
            'disponible'=>'0'
        );

        // Idem, mais ici c'est logique le get renvoie
        // array([0]=>null)
        $expected_del = null;

        // Blabla, vous avez compris
        $expected_search = array(
            array(
                'id'=>'2',
                'isbn'=>null,
                'titre'=>'Le petit Prince',
                'auteur'=>'Antoine de Saint-Exupéry',
                'edition'=>'Gallimard',
                'parution'=>'2017-10-12',
                'couverture'=>'assets/img/livres/2.jpeg',
                'description'=>'',
                'disponible'=>'1'
            ),
            array(
                'id'=>'8',
                'isbn'=>null,
                'titre'=>'Le petit Nicolas s amuse',
                'auteur'=>'Sempé / Goscinny',
                'edition'=>'Gallimard',
                'parution'=>'2017-10-12',
                'couverture'=>'assets/img/livres/8.jpg',
                'description'=>'',
                'disponible'=>'0'
            )
        );

        // On rentre dans le dur ;)
        // La variable obtained va etre celle qui recupere les resultat des fonction testées
        $obtained = $this->livre->get(array('id'=>$livre_id));
        // Et ici tout simplement, on ajoute le joli rapport générer par CI au tableau global de resultat
        // Important, les resultat doivent TOUJOURS etre spécifiées sous cette forme, a savoir :
        // ici ['livre'] ca dit qu'on teste le model des livre
        // et ['get'] c'est le nom de la fonction
        // Les parametres du unit->run(le resultat obtenu, le resultat attendu, un petit nom sympa pour le rapport)
        $result['livre']['get'] = $this->unit->run($obtained,$expected_get,'livre->get');

        // Meme combat, mais ici plus d'étape, d'abord on ajoute, la valeur de retour devra être tester mais la,
        // la flemme
        // Et on fait le fameux get pour tester l'insertion
        $this->livre->add($expected_add);
        $obtained = $this->livre->get(array('titre'=>$expected_add['titre']))[0];
        $expected_add['id'] = $obtained['id'];
        $result['livre']['add'] = $this->unit->run($obtained,$expected_add, 'livre->add');

        $expected_set['id'] = $obtained['id'];
        $this->livre->set($expected_set);
        $obtained = $this->livre->get(array('id'=>$expected_set['id']))[0];
        $result['livre']['set'] = $this->unit->run($obtained,$expected_set,'livre->set');

        $this->livre->del(array('id'=>$expected_set['id']));
        $obtained = $this->livre->get(array('id'=>$expected_set['id']));
        $result['livre']['del'] = $this->unit->run($obtained,$expected_del,'livre->del');

        $obtained = $this->livre->search('le petit');
        $result['livre']['search'] = $this->unit->run($obtained,$expected_search,'livre->search');

        // Important, copier coller la boucle a la fin de chaque test, c'est juste un apercu globale
        // des test effectués et des retour positif
        foreach ($result['livre'] as $test){
            if (strpos($test,"Passed")){
                $this->testPassed++;
            }
            $this->testNB++;
        }

        // Logique on renvoie le tableau de resultat de ce test
        return $result;

        // La meme structure est a appliquer pour tout les test,
        // Pourquoi? Parce que j'ai mis en place dans la vue une petite boucle sympa qui affiche tout comme il faut
        // En plus avec des titres donc respectez mon travail
        // Si vous êtes bloquer dans un test, usez et abusez des dump(), c'est votre meilleur ami, en cas de gros dump :
        // https://www.diffchecker.com/diff et oui je vous respect je vous fait pas faire ça en ligne de commande!
    }

    private function userTest()
    {
        $result = array();

        $expected_get[0] = array(
            'id'=>'1',
            'identifiant'=>'admin',
            'nom'=>'Jean-Gui',
            'prenom'=>'Ladmin',
            'role'=>'1',
            'motdepasse'=>'$2y$10$PSRun0QaBZUWKhhFMoRNhetlscCgvjOuq36XbnNguew1v.uF4Nlai'
        );

        $expected_prof_user[0] = array(
            'id'=>'',
            'identifiant'=>'jsprof',
            'nom'=>'John',
            'prenom'=>'Doe',
            'role'=>'2',
            'motdepasse'=>'test'
        );

        $expected_child_user[0] = array(
            'identifiant'=>'jschild',
            'nom'=>'John',
            'prenom'=>'Doe',
            'role'=>'3',
            'classe'=>'1',
            'pastille'=>'turtle'
        );

        $expected_del = array();

        // ************* Get user test
        $obtained = $this->user->get(array('id'=>'1'));
        $result['user']['get'] = $this->unit->run($obtained,$expected_get,'user->get');

        // ************** Add user test
        $this->user->add($expected_prof_user[0]);
        $obtained = $this->user->get(array('identifiant'=>$expected_prof_user[0]['identifiant']));
        $expected_prof_user[0]['id'] = $obtained[0]['id'];
        $expected_prof_user[0]['motdepasse'] = $obtained[0]['motdepasse'];
        $result['user']['add_prof'] = $this->unit->run($obtained,$expected_prof_user,'user->add_prof');

        $this->user->add($expected_child_user[0]);
        $obtained = $this->user->get(array('identifiant'=>$expected_child_user[0]['identifiant']));
        $expected_child_user[0]['id'] = $obtained[0]['id'];
        $expected_child_user[0]['pastille'] = $obtained[0]['pastille'];
        $expected_child_user[0]['classe'] = $obtained[0]['classe'];
        $result['user']['add_child'] = $this->unit->run($obtained,$expected_child_user,'user->add_child');

        // *************** Update user test
        $expected_prof_user_set = $expected_prof_user;
        $expected_prof_user_set[0]['nom'] = 'foo';
        $expected_prof_user_set[0]['prenom'] = 'bar';
        $this->user->set($expected_prof_user_set[0]);
        $obtained = $this->user->get(array('id'=>$expected_prof_user_set[0]['id']));
        $expected_prof_user_set[0]['motdepasse'] = $obtained[0]['motdepasse'];
        $result['user']['set_prof'] = $this->unit->run($obtained,$expected_prof_user_set,'user->set_prof');

        $expected_child_user_set = $expected_child_user;
        $expected_child_user_set[0]['nom'] = 'foo';
        $expected_child_user_set[0]['prenom'] = 'bar';
        $expected_child_user_set[0]['pastille'] = 'panda';
        $this->user->set($expected_child_user_set[0]);
        $obtained = $this->user->get(array('id'=>$expected_child_user_set[0]['id']));
        $result['user']['set_child'] = $this->unit->run($obtained,$expected_child_user_set,'user->set_child');

        // ***************** Delete
        $this->user->del(array('id'=>$expected_prof_user[0]['id']));
        $obtained = $this->user->get(array('id'=>$expected_prof_user[0]['id']));
        $result['user']['del_prof'] = $this->unit->run($obtained,$expected_del,'user->del_prof');

        $this->user->del(array('id'=>$expected_child_user[0]['id']));
        $obtained = $this->user->get(array('id'=>$expected_child_user[0]['id']));
        $result['user']['del_child'] = $this->unit->run($obtained,$expected_del,'user->del_child');

        foreach ($result['user'] as $test){
            if (strpos($test,"Passed")){
                $this->testPassed++;
            }
            $this->testNB++;
        }

        return $result;
    }

    private function empruntTest()
    {
        $expected_add[0]= array('id_livre'=>'1','id_eleve'=>'3','dateEmprunt'=>'2018-08-03','dateRendu'=>NULL);

        $this->emprunt->add($expected_add[0]);
        $obtained = $this->emprunt->get(array('id_livre'=>$expected_add[0]['id_livre']));
        $result['emprunt']['add'] = $this->unit->run($obtained,$expected_add, 'emprunt->add');

        $this->emprunt->del($expected_add[0]);

        foreach ($result['emprunt'] as $test){
            if (strpos($test,"Passed")){
                $this->testPassed++;
            }
            $this->testNB++;
        }

        return $result;
    }

    private function classTest() // TODO
    {
        $expected_add[0]= array('libelle'=>'CP');
        $expected_set[0]= array('libelle'=>'CM1');

        $this->classe->add($expected_add[0]);
        $obtained = $this->classe->get();
        $result['classe']['add'] = $this->unit->run($obtained,$expected_add, 'emprunt->add');

        $this->classe->set($expected_set[0]);

        $this->classe->del($expected_add[0]);

        foreach ($result['class'] as $test){
            if (strpos($test,"Passed")){
                $this->testPassed++;
            }
            $this->testNB++;
        }

        return $result;
    }

    private function themeTest() // TODO
    {
        
    }

    private function apiTest() // TODO
    {
        
    }

    private function resetAI()
    {
        foreach (self::$tables as $table){
            $this->db->query('ALTER TABLE '.$table.' AUTO_INCREMENT=10');
        }
    }
}