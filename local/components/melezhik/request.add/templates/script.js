$(function(){
    new SlimSelect({
        select: ".BUDZHET",
        showSearch: true,
        placeholder: 'Выберите бюджет',
        beforeOpen: function beforeOpen() {}
    });

    new SlimSelect({
        select: ".search-elements",
        showSearch: true,
        placeholder: '',
        beforeOpen: function beforeOpen() {}
    });

    var catalog_select= new SlimSelect({
        select: ".catalog-elements",
        showSearch: true,
        placeholder: 'Выберите товар',
        beforeOpen: function beforeOpen() {}
    });

    $(document).on("change", ".search-elements", function() {
        var id=$(this).val();
        if (id>0) {
            $.post(
                '/local/tools/ajax/sinteka/budzhet/find_elements.php',
                {id:id}
            )
                .done(function(data) {
                    if (data=="error") {
                        catalog_select.setData([]);
                        $('.add-tovar').hide();
                    } else {
                        var json_res=JSON.parse(data);
                        catalog_select.setData(json_res);
                        $('.add-tovar').show();
                    }
                });
        } else {
            catalog_select.setData([]);
            $('.add-tovar').hide();
        }
    });

    $(document).on("click", ".add-tovar", function() {
        var elements = $('.catalog-elements').val();
        if (elements.length > 0) {
            $.post(
                '/local/tools/ajax/sinteka/budzhet/add_elements.php',
                {id: elements}
            )
                .done(function (data) {
                    console.log(data);
                    if (data == "error") {
                    } else {
                        var json_res = JSON.parse(data);
                        json_res.forEach(function (item, index, array) {
                            if ($('.catalog-row[data-id="' + item.ID + '"]').length) {
                                console.log("Такой элемент уже есть его добавлять не надо!!!");
                            } else {
                                $('.tovars-no-bud .elements-table tbody').append("<tr class='catalog-row' data-id='" + item.ID + "'><td>" + item.NAME + "</td><td><input class='price' type='number' min='0' size='3' value='" + item.PRICE + "'></td><td><input class='num-catalog-item-comp' type='number' min='0' size='3'></td><td><span class='sum-price-row'>0</span><span class='row-close'>X</span></td></tr>");
                            }
                        });
                    }
                });
        }
    });

    $(document).on("keyup", ".num-catalog-item-comp", function() {
        var val=$(this).val();
        var parent_tr=$(this).closest('tr');
        var id_row=parent_tr.data('id');
        var price=parent_tr.find('.price').val();
        var vol_all=val*price;
        parent_tr.find('.sum-price-row').text(vol_all.toFixed(2));
        sum_all();
    });

    $(document).on("keyup", ".price", function() {
        var parent_tr=$(this).closest('tr');
        var val=parent_tr.find('.num-catalog-item-comp').val();
        var price=parent_tr.find('.price').val();
        var vol_all=val*price;
        parent_tr.find('.sum-price-row').text(vol_all.toFixed(2));
        sum_all();
    });

    $(document).on("click", ".row-close", function() {
        $(this).closest('tr').remove();
        sum_all();
    });

    function sum_all() {
        var sum_all=0;
        $('.tovars-no-bud .catalog-row').each(function(){
            var price=$(this).find('.price').val();
            var num=$(this).find('.num-catalog-item-comp').val();
            sum_all+=num*price;
        });
        $('.sum-all-budzhet').text(sum_all.toFixed(2));
    }


    $(document).on('click', "input[name='from_b']", function(){
        var chek=$("input[name='from_b']:checked").val();
        if (chek=='N') {
            $('.tovars-no-bud').show();
        } else {
            $('.tovars-no-bud').hide();
        }
    });


    $(document).on('change', '.BUDZHET', function(){
        var bud=$(this).val();
        console.log(bud);
        if (bud>0) {
            BX.ajax.runComponentAction('art:request.add',
                'addTovars', { // Вызывается без постфикса Action
                    mode: 'class',
                    data: {post: {bud: bud}}, // ключи объекта data соответствуют параметрам метода
                })
                .then(function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        console.log(response);
                        $('.block-tovars').html(response.data);
                        // Если форма успешно отправилась
                    }
                });
        } else {
            $('.block-tovars').html("");
        }

    });

    $(document).on('click', '.request-create', function(){
        var bud=$('.BUDZHET').val();
        var chek=$("input[name='from_b']:checked").val();
        var prod={};
        var nn=0;
        $('.catalog-row').each(function(){
            var id=$(this).data('id');
            var price=$(this).find('.price').val();
            var num=$(this).find('.num-catalog-item-comp').val();
            if (num>0) nn++;
            prod[id]= {
                PRODUCT_ID: id,
                QUANTITY: num,
                PRICE: price
            };
        });
        if (bud>0 && nn>0 && chek) {
            console.log('can create req');
            BX.ajax.runComponentAction('art:request.add',
           'requestCreate', { // Вызывается без постфикса Action
               mode: 'class',
               data: {post: {bud: bud, chek: chek, products: prod}}, // ключи объекта data соответствуют параметрам метода
           })
           .then(function(response) {
               if (response.status === 'success') {
                   console.log(response);
                   $('.block-good').html("Заявка успешно создана!!!");
                   //$('.block-good').html(response.data);
                   // Если форма успешно отправилась
                   window.location.href="/page/sinteka/zayavka_na_materialysinteka/";
               }
           });
        } else {
            console.log('Нельзя создавать заявку не заполенно одно из полей');
        }
    });

});
