
$('#bookSelector').on('change', function (elm) {
    if (elm.target.value === "all"){
        filterBook('all','');
    } else {
        filter = elm.target.value;
    }
});

$('#assign_theme').on('click', function () {
    let checkbox = $('#filter_book_container').find('input:checked');
    if ( checkbox.length > 0 && themeToAdd.length > 0){
        var bookList = [];
        checkbox.each(function () {
            bookList.push(this.id);
        });

        $.post('ajax/assignThemeToBook',{books:bookList,themes:themeToAdd}, function (responseText) {
           if (responseText === SUCCESS){
               Materialize.toast('Les livres ont été affectés avec succès.',5000);
               $('#theme_add_chips').material_chip();
               checkbox.prop('checked', false);
           }else if (responseText === FAILURE){
               Materialize.toast(ERROR_MESSAGE,5000);
           }
        });
    } else {
        Materialize.toast('Entrez des livres ou des thèmes à ajouter avant', 5000)
    }
});

$('#themeType').on('change', function () {
   if (this.value === "main_"){
       $('#theme_file_container').prop('hidden', false);
   } else {
       $('#theme_file_container').prop('hidden', true);
   }
});

function addTheme() {
    let data = {};
    let themeType = $('#themeType').val();

    data['nom'] =  themeType + $('#theme').val();
    if (themeType === "main_"){
        data['file'] = 'true';
    }

    $.post('ajax/addTheme', data, function (responseText) {
        if (responseText === SUCCESS){
            Materialize.toast('Le theme a été ajouté avec succès', 5000);
            $('#theme').val('');
        }else if(responseText === FAILURE){
            Materialize.toast('Une erreur s\'est produite, réessayez plus tard ou contactez un administrateur', 5000);
        }
    })
}

function stylize() {
    $('tbody').css({
        display:'block',
        height:'300px',
        overflow:'auto',
    });

    $('thead, tbody tr').css({
        display:'table',
        width:'100%',
        'table-layout':'fixed'
    });

    $('thead').css('width','calc( 100% - 1em)');

    $('table').css('width','100%');
}

function filterBook(filter, data) {
    $.post('ajax/filterBook',{filter:filter,data:data}, function (responseText) {
        if (responseText === UNKNOWN){
            Materialize.toast('Aucun livre ne correspond a votre recherche.', 5000);
        }else if(responseText === FAILURE){
            Materialize.toast(ERROR_MESSAGE, 5000);
        }else {
            $('#filter_book_container').html(responseText);
        }
    })
}

