@extends('layouts.default')

@section('head')

<title>Arquigrafia - Seu universo de imagens de arquitetura</title>

<!-- ISOTOPE -->
<script src="{{ URL::to("/") }}/js/jquery.isotope.min.js"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/panel.js"></script>
<!--Pickers -->
<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->

<!-- AUTOCOMPLETE -->
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.core.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.tags.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/styletags.css" />

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.core.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.tags.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.autocomplete.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.suggestions.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.filter.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tags-autocomplete.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.ajax.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tag-list.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tag-autocomplete-part.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/city-autocomplete.js" charset="utf-8"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />

@stop

@section('content')
    @if (isset($message))
    <div class="container">
      <div class="twelve columns">
        <div class="message">{{ $message }}</div>
      </div>
    </div>
  @endif
    <!--   MEIO DO SITE - ÁREA DE NAVEGAÇÃO   -->
    <div id="content">
      <div class="container">
        <div id="search_result" class="twelve columns row">
          <h1>Busca avançada</h1>
          <div class="twelve columns alpha">
            <p>
            Apenas os campos que forem preenchidos abaixo serão considerados na busca,
             para trazer as imagens que correspondam a todos os critérios informados.
            </p>
          </div>
        </div>
        {{ Form::open(array('url' => 'search/more', 'method' => 'get')) }}
          <div class="eight columns omega row">
            <div class="eight columns alpha omega row">
              <div class="four columns alpha omega">
                <h3>Descrição</h3>
                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('name', 'Título da imagem:') }}
                    </td>
                    <td>
                      {{ Form::text('name', Input::get("name") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      {{ Form::label('description', 'Descrição da imagem:') }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      {{ Form::textarea('description', Input::get("description"),
                        array('cols' => 40, 'rows' => 3)) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('tags_input', 'Tags*:') }}
                      <p style="font-size: 7pt">Máximo 5 tags</p>
                    </td>
                    <td>
                      <div class="two columns alpha" style="width: 150px !important;">
                        {{ Form::text('tags_input') }} <br>
                        <p>
                          <button class="btn right" id="add_tag">ADICIONAR TAG</button>
                        </p>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <textarea name="tags" id="tags" cols="35" rows="1" style="display: none;"></textarea>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="error"></div>
                    </td>
                  </tr>
                </table>
              </div>
              <!-- 2015-05-06 msy begin, workAuthor -->
              <div class="four columns alpha omega">
                <h3>Arquitetura</h3>
                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('workAuthor', 'Autor da obra:') }}
                    </td>
                    <td>
                      {{ Form::text('workAuthor', Input::get("workAuthor") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('imageAuthor', 'Autor da imagem:') }}
                    </td>
                    <td>
                      {{ Form::text('imageAuthor', Input::get("imageAuthor") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('workdate', 'Data da obra:') }}
                    </td>
                    <td>
                      {{ Form::text('workdate',Input::get("workdate"),
                        array('id' => 'datePickerWorkDate')) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('dataCriacao', 'Data da imagem:') }}
                    </td>
                    <td>
                      {{ Form::text('dataCriacao',Input::get("dataCriacao"),
                        array('id' => 'datePickerdataCriacao')) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('dataUpload', 'Data de upload:') }}
                    </td>
                    <td>
                      {{ Form::text('dataUpload',Input::get("dataUpload"),
                        array('id' => 'datePickerdataUpload')) }}
                    </td>
                  </tr>
                </table>
              </div>
              <!-- 2015-05-06 msy end -->
            </div>
            <div class="eight columns alpha omega row">
              <div class="four columns alpha omega">
                <h3>Localização</h3>
                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('country', 'País:') }}
                    </td>
                    <td>
                      {{ Form::select('country', [ "Afeganistão"=>"Afeganistão", "África do Sul"=>"África do Sul", "Albânia"=>"Albânia", "Alemanha"=>"Alemanha", "América Samoa"=>"América Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Anguilla"=>"Anguilla", "Antartida"=>"Antartida", "Antigua"=>"Antigua", "Antigua e Barbuda"=>"Antigua e Barbuda", "Arábia Saudita"=>"Arábia Saudita", "Argentina"=>"Argentina", "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Barbados"=>"Barbados", "Bélgica"=>"Bélgica", "Belize"=>"Belize", "Bermuda"=>"Bermuda", "Bhutan"=>"Bhutan", "Bolívia"=>"Bolívia", "Botswana"=>"Botswana", "Brasil"=>"Brasil", "Brunei"=>"Brunei", "Bulgária"=>"Bulgária", "Burundi"=>"Burundi", "Cabo Verde"=>"Cabo Verde", "Camboja"=>"Camboja", "Canadá"=>"Canadá", "Chade"=>"Chade", "Chile"=>"Chile", "China"=>"China", "Cingapura"=>"Cingapura", "Colômbia"=>"Colômbia", "Djibouti"=>"Djibouti", "Dominicana"=>"Dominicana", "Emirados Árabes"=>"Emirados Árabes", "Equador"=>"Equador", "Espanha"=>"Espanha", "Estados Unidos"=>"Estados Unidos", "Fiji"=>"Fiji", "Filipinas"=>"Filipinas", "Finlândia"=>"Finlândia", "França"=>"França", "Gabão"=>"Gabão", "Gaza Strip"=>"Gaza Strip", "Ghana"=>"Ghana", "Gibraltar"=>"Gibraltar", "Granada"=>"Granada", "Grécia"=>"Grécia", "Guadalupe"=>"Guadalupe", "Guam"=>"Guam", "Guatemala"=>"Guatemala", "Guernsey"=>"Guernsey", "Guiana"=>"Guiana", "Guiana Francesa"=>"Guiana Francesa", "Haiti"=>"Haiti", "Holanda"=>"Holanda", "Honduras"=>"Honduras", "Hong Kong"=>"Hong Kong", "Hungria"=>"Hungria", "Ilha Cocos (Keeling)"=>"Ilha Cocos (Keeling)", "Ilha Cook"=>"Ilha Cook", "Ilha Marshall"=>"Ilha Marshall", "Ilha Norfolk"=>"Ilha Norfolk", "Ilhas Turcas e Caicos"=>"Ilhas Turcas e Caicos", "Ilhas Virgens"=>"Ilhas Virgens", "Índia"=>"Índia", "Indonésia"=>"Indonésia", "Inglaterra"=>"Inglaterra", "Irã"=>"Irã", "Iraque"=>"Iraque", "Irlanda"=>"Irlanda", "Irlanda do Norte"=>"Irlanda do Norte", "Islândia"=>"Islândia", "Israel"=>"Israel", "Itália"=>"Itália", "Iugoslávia"=>"Iugoslávia", "Jamaica"=>"Jamaica", "Japão"=>"Japão", "Jersey"=>"Jersey", "Kirgizstão"=>"Kirgizstão", "Kiribati"=>"Kiribati", "Kittsnev"=>"Kittsnev", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesotho"=>"Lesotho", "Líbano"=>"Líbano", "Líbia"=>"Líbia", "Liechtenstein"=>"Liechtenstein", "Luxemburgo"=>"Luxemburgo", "Maldivas"=>"Maldivas", "Malta"=>"Malta", "Marrocos"=>"Marrocos", "Mauritânia"=>"Mauritânia", "Mauritius"=>"Mauritius", "México"=>"México", "Moçambique"=>"Moçambique", "Mônaco"=>"Mônaco", "Mongólia"=>"Mongólia", "Namíbia"=>"Namíbia", "Nepal"=>"Nepal", "Netherlands Antilles"=>"Netherlands Antilles", "Nicarágua"=>"Nicarágua", "Nigéria"=>"Nigéria", "Noruega"=>"Noruega", "Nova Zelândia"=>"Nova Zelândia", "Omã"=>"Omã", "Panamá"=>"Panamá", "Paquistão"=>"Paquistão", "Paraguai"=>"Paraguai", "Peru"=>"Peru", "Polinésia Francesa"=>"Polinésia Francesa", "Polônia"=>"Polônia", "Portugal"=>"Portugal", "Qatar"=>"Qatar", "Quênia"=>"Quênia", "República Dominicana"=>"República Dominicana", "Romênia"=>"Romênia", "Rússia"=>"Rússia", "Santa Helena"=>"Santa Helena", "Santa Kitts e Nevis"=>"Santa Kitts e Nevis", "Santa Lúcia"=>"Santa Lúcia", "São Vicente"=>"São Vicente", "Singapura"=>"Singapura", "Síria"=>"Síria", "Spiemich"=>"Spiemich", "Sudão"=>"Sudão", "Suécia"=>"Suécia", "Suiça"=>"Suiça", "Suriname"=>"Suriname", "Swaziland"=>"Swaziland", "Tailândia"=>"Tailândia", "Taiwan"=>"Taiwan", "Tchecoslováquia"=>"Tchecoslováquia", "Tonga"=>"Tonga", "Trinidad e Tobago"=>"Trinidad e Tobago", "Turksccai"=>"Turksccai", "Turquia"=>"Turquia", "Tuvalu"=>"Tuvalu", "Uruguai"=>"Uruguai", "Vanuatu"=>"Vanuatu", "Wallis e Fortuna"=>"Wallis e Fortuna", "West Bank"=>"West Bank", "Yémen"=>"Yémen", "Zaire"=>"Zaire", "Zimbabwe"=>"Zimbabwe"],"Brasil") }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('state', 'Estado:') }}
                    </td>
                    <td>
                      {{ Form::select('state', [""=>"Escolha o Estado", "AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá", "BA"=>"Bahia", "CE"=>"Ceará", "DF"=>"Distrito Federal", "ES"=>"Espirito Santo", "GO"=>"Goiás", "MA"=>"Maranhão", "MG"=>"Minas Gerais", "MS"=>"Mato Grosso do Sul", "MT"=>"Mato Grosso", "PA"=>"Pará", "PB"=>"Paraíba", "PE"=>"Pernambuco", "PI"=>"Piauí", "PR"=>"Paraná", "RJ"=>"Rio de Janeiro", "RN"=>"Rio Grande do Norte", "RO"=>"Rondônia", "RR"=>"Roraima", "RS"=>"Rio Grande do Sul", "SC"=>"Santa Catarina", "SE"=>"Sergipe", "SP"=>"São Paulo", "TO"=>"Tocantins"], Input::get("state") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('city', 'Cidade:') }}
                    </td>
                    <td>
                      {{ Form::text('city', Input::get("city") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('district', 'Bairro:') }}
                    </td>
                    <td>
                      {{ Form::text('district', Input::get("district") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('street', 'Endereço:') }}
                    </td>
                    <td>
                      {{ Form::text('street', Input::get("street") ) }}
                    </td>
                  </tr>
                </table>
              </div>
              <div class="four columns alpha omega">
                <h3>Licença das imagens</h3>
                <table class="form-table" width="80%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('allowCommercialUses', 'Com uso comercial:') }}
                    </td>
                    <td>
                      {{ Form::select('allowCommercialUses',
                        [""=>"Escolha", "YES"=>"Sim", "NO"=>"Não"],
                        Input::get("allowCommercialUses") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('allowModifications', 'Permitem alteração:') }}
                    </td>
                    <td>
                      {{ Form::select('allowModifications',
                        [""=>"Escolha", "YES"=>"Sim", "NO"=>"Não"],
                        Input::get("allowModifications") ) }}
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="six columns alpha row">
              <p>{{ Form::submit('BUSCAR', ['class'=>'btn']) }}</p>
            </div>
          </div>
          <div class="four columns alpha row">
            <div class="four columns alpha omega">
              <h3>Interpretação das imagens</h3>
              <p style="text-align: justify">
                Ao indicar valores nos binômios abaixo,
                você fará uma busca por imagens que possuem resultados semelhantes,
                considerando um intervalo de 5 pontos acima
                e abaixo do valor que você selecionar.
              </p>
              <br>
              {{ Form::checkbox('binomial_check', 1, false) }}
              {{ Form::label('binomial_check', 'Utilizar binômios na pesquisa') }}
              <br><br>
              <div id="binomial_container" class="four columns alpha row hidden">
                <?php $count = $binomials->count() - 1; ?>
                @foreach($binomials->reverse() as $binomial)
                  <?php $diff = $binomial->defaultValue ?>
                  <p>
                    <table border="0" width="230">
                      <tr>
                        <td width="110">
                            {{ $binomial->firstOption }}
                            (<output for="fader{{ $binomial->id }}"
                              id="leftBinomialValue{{ $binomial->id }}">
                              {{ 100 - $diff }}
                            </output>%)
                        </td>
                        <td align="right">
                            {{ $binomial->secondOption }}
                            (<output for="fader{{ $binomial->id }}"
                              id="rightBinomialValue{{ $binomial->id }}">
                              {{ $diff }}
                            </output>%)
                        </td>
                      </tr>
                    </table>
                    {{ Form::input('range', 'value-'.$binomial->id, $diff,
                      [ 'min' => '0',
                        'max' => '100',
                        'oninput' => 'outputUpdate(' . $binomial->id . ', value)',
                        'disabled' => true,
                        'class' => 'binomial_value' ])
                    }}
                  </p>
                  <?php $count-- ?>
                @endforeach
              </div>
            </div>
          </div>
        {{ Form::close() }}

      </div>
      @if (count($photos))
        <!--   PAINEL DE IMAGENS - GALERIA - CARROSSEL   -->
        <div class="wrap">
          <div id="panel">
            @include('includes.panel')
          </div>
  		    <div class="panel-back"></div>
          <div class="panel-next"></div>
        </div>
      <!--   FIM - PAINEL DE IMAGENS  -->
    @endif
    </div>
    <!--   FIM - MEIO DO SITE   -->
    <script type="text/javascript">
      $(document).ready(function() {
        $('input[name="binomial_check"]').click(function(e) {
          if ( $(this).prop('checked') ) {
            $('#binomial_container').removeClass('hidden');
            $('.binomial_value').prop('disabled', false);
          } else {
            $('.binomial_value').prop('disabled', true);
            $('#binomial_container').addClass('hidden');
          }
        });

        $('#tags').textext({ plugins: 'tags' });
        @if ( isset($tags) )
          @foreach ( $tags as $tag )
            $('#tags').textext()[0].tags().addTags([ {{ '"' . $tag . '"' }} ]);
          @endforeach
        @endif
        //var h = document.getElementsById('text-label').value;
        //h.value = 100;
        //alert(h);
        //var clicks = 0;

        $('#tags_input').keypress(function(e) {
          var key = e.which || e.keyCode;
          if (key == 44 || key == 46 || key == 59){ // key = , ou Key = . ou key = ;
            e.preventDefault();
            // clicks += 1;
            // alert(clicks);
          }
        });
      });
      $(function() {
        $( "#datePickerWorkDate" ).datepicker({
          dateFormat:'dd/mm/yy',
          keyboardNavigation: true,
          orientation: "bottom right"
        });
        $( "#datePickerdataCriacao" ).datepicker({
          dateFormat:'dd/mm/yy',
          keyboardNavigation: true,
          orientation: "bottom right"
        });
        $( "#datePickerdataUpload" ).datepicker({
          dateFormat:'dd/mm/yy',
          beforeShow: function(datePickerdataUpload) {
            $(datePickerdataUpload).css({
              "position":"relative",
              "z-index":999999
            });
          }
        });
      });

      function outputUpdate(binomio, val) {
        var left, right;
        left = document.querySelector('#leftBinomialValue'+binomio);
        right = document.querySelector('#rightBinomialValue'+binomio);
        left.value = 100 - val;
        right.value = val;
      }
    </script>
@stop