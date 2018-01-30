add_zabor='<form id="form-zabor-'+ smeta_number +'" action="/web/index.php?r=site%2Fcalcsmeta" method="post">';
add_zabor='<input name="smeta_number" class="hidden" value="' + smeta_number + '"/>';
add_zabor='<div class="row"><div class="col-xs-2">';
add_zabor+='<div class="form-group field-zabor-type">';
add_zabor+='<label class="control-label" for="zabor-type[' + smeta_number +']">Тип работ</label>';
add_zabor+='<select id="zabor-type[' + smeta_number +']" class="form-control" name="Zabor[type][' + smeta_number +']">';
add_zabor+='<option value="none">Тип работ</option>';
add_zabor+='<option value="calcproflist">Профлист</option>';
add_zabor+='<option value="shtaket">Штакетник</option>';
add_zabor+='<option value="evroshtaket">Евроштакетник</option>';
add_zabor+='<option value="rabica">Сетка рабица</option>';
add_zabor+='option value="rabicaramka">Рабица в рамке</option>';
add_zabor+='<option value="kalitka">Калитка</option>';
add_zabor+='<option value="vorota">Ворота</option>';
add_zabor+='<option value="fundament">Ленточный фундамент</option>';
add_zabor+='<option value="svai">Сваи</option>';
add_zabor+='option value="parkovka">Парковочное место</option>';
add_zabor+='option value="kanava">Вьезд через канаву</option>';
add_zabor+='</select>';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';

add_zabor+='<div class="col-xs-1"><div class="form-group field-zabor-h">';
add_zabor+='<label class="control-label" for="zabor-h[' + smeta_number +']">Высота</label>';
add_zabor+='<input type="text" id="zabor-h[' + smeta_number +']" class="form-control" name="Zabor[h][' + smeta_number +']">';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';
add_zabor+='<div class="col-xs-1"><div class="form-group field-zabor-w">';
add_zabor+='<label class="control-label" for="zabor-w">Ширина</label>';
add_zabor+='<input type="text" id="zabor-w[' + smeta_number +']" class="form-control" name="Zabor[w][' + smeta_number +']">';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';
add_zabor+='<div class="col-xs-2"><div class="form-group field-zabor-summa">';
add_zabor+='<label class="control-label" for="zabor-summa[' + smeta_number +']">Сумма работ</label>';
add_zabor+='<input type="text" id="zabor-summa[' + smeta_number +']" class="form-control" name="Zabor[summa][' + smeta_number +']" readonly="true">';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';

add_zabor+='<div class="col-xs-2 alcenter" style="padding-top:12px"><br/>';

add_zabor+='<button type="button" id="btn-calc[' + smeta_number +']" name="btn-calc" class="btn-default">РАССЧИТАТЬ</button><br/>';
add_zabor+='</div>';

add_zabor+='</div></form>';