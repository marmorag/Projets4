<?php
$data['title'] = 'Historique';
$data['env'] = 'log';
$this->load->view('utilities/page_head', $data);
$this->load->view('utilities/page_nav', $data);

?>

<div id="modal1" class="modal">
    <div class="modal-content">
        <h4>Attention!</h4>
        <blockquote>La suppression d'un livre est définitive. Etes vous sur de vouloir continuer ?</blockquote>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="agree()" class="modal-action modal-close waves-effect waves-green btn-flat">Continuer</a>
        <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Annuler</a>
    </div>
</div>

<div class="container">
    <br>
    <br>
    <br>
    <br>
    <ul class="collapsible" data-collapsible="accordion">
        <li>
            <div class="collapsible-header">
                <i class="material-icons">add</i>
                Ajouter livre
            </div>
            <div class="collapsible-body">
                <form id="book_form">
                <div class="row">
                    <div class="input-field col s6">
                        <input name="isbn" id="isbn" type="text" data-length="13" maxlength="13">
                        <label class="red-text ligthen-2" for="isbn">ISBN</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <input name="titre" id="titre" type="text" class="validate">
                        <label class="red-text ligthen-2" for="titre">Titre</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <input name="auteur" id="auteur" type="text" class="validate">
                    <label class="red-text ligthen-2" for="auteur">Auteur</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <input name="edition" id="edition" type="text" class="validate">
                        <label class="red-text ligthen-2" for="edition">Edition</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <input name="parution" id="parution" type="text" class="datepicker">
                        <label class="red-text ligthen-2" for="parution">Parution</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <textarea name="description" id="description" class="materialize-textarea" data-length="500" maxlength="500"></textarea>
                        <label class="red-text ligthen-2" for="description">Résumé</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
<!--                        <div class="file-field input-field ">-->
<!--                            <div class="btn red lighten-3">-->
<!--                                <span>Image</span>-->
<!--                                <input name="couverture" type="file">-->
<!--                            </div>-->
<!--                            <div class="file-path-wrapper">-->
                                    <input class="file-path validate" type="text" name="couverture" id="couverture">
                                    <label class="red-text ligthen-2" for="couverture">Path couverture</label>
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                </div>
                 <button class="btn waves-effect waves-light red lighten-3 " type="button" onclick="addBook()" name="save">Enregistrer
                 <i class="material-icons rigth ">save</i>
                </button>
                </form>
            </div>
        </li>
        <li>
            <div class="collapsible-header">
                <i class="material-icons">library_books</i>
                Modifier/Supprimer un livre
            </div>
            <div class="collapsible-body">
                <span>
                    <div class="row">
                        <div id="catalogue_container" class="input-field col s12">
                            <i id="search" class="material-icons prefix">search</i>
                            <div class="chips-placeholder"></div>
                        </div>
                    </div>
                    <ul id="book_container" class="collection with-header">
                    </ul>
                </span>
            </div>
        </li>
    </ul>
</div>



<?php

$data['load'] = array('jquery.min','materialize.min','ajax','chips','datepicker','gestionbu');
$this->load->view('utilities/page_footer', $data); ?>
