	
  <!-- ANALYTICS -->
  <script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-20571872-1']);
		_gaq.push(['_trackPageview']);
	
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
  <script src="{{ URL::to("/") }}/js/script.js"></script>


<!-- Crazy Egg -->
<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//script.crazyegg.com/pages/scripts/0035/4321.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>  
  
  <!--   RODAPE DO SITE   -->
  <div id="footer" class="container">
    
		<div class="twelve columns">
	
      <!--   CRÉDITOS - LOGOS   -->
      <div id="credits" class="clearfix">
        <ul class="footer-links">
          @if( Auth::guest() )
          <li><a href="{{ URL::to("/users/login") }}">Login</a></li>
          <li><a href="{{ URL::to("/users/account") }}">Cadastrar-se</a></li>
          @endif
          <li><a href="{{ URL::to("/") }}/project">O projeto</a></li>
          <li><a href="{{ URL::to("/") }}/faq">FAQ</a></li>

         <!-- <li><script>document.write('<'+'a'+' '+'h'+'r'+'e'+'f'+'='+"'"+'m'+'a'+'i'+'l'+'&'+'#'+'1'+'1'+'6'+';'+'o'+'&'+'#'+'5'+'8'+';'+
'p'+'e'+'d'+'r'+'%'+'6'+'F'+'&'+'#'+'6'+'4'+';'+'&'+'#'+'3'+'7'+';'+'7'+'2'+'c'+'&'+'#'+'1'+'0'+'7'+
';'+'t'+'&'+'#'+'4'+'6'+';'+'c'+'o'+'&'+'#'+'1'+'0'+'9'+';'+'&'+'#'+'3'+'7'+';'+'&'+'#'+'5'+'0'+';'+
'&'+'#'+'6'+'9'+';'+'%'+'6'+'2'+'%'+'7'+'2'+"'"+'>Contato<'+'/'+'a'+'>'
);</script><noscript>Contato, funciona apenas com Javascript.</noscript></li>-->
        <li><a href="mailto: arquigrafiabrasil@gmail.com">Contato</a></li>

        </ul>
        <ul>
          <li><a href="http://www.usp.br/" title="USP" id="usp" target="_blank"></a></li>
          <li><a href="http://www.fapesp.br/" title="FAPESP" id="fapesp" target="_blank"></a></li>
          <li><a href="http://www.rnp.br/" title="RNP" id="rnp" target="_blank"></a></li>
        </ul>
        <ul>
          <li><a href="http://www.cnpq.br/" title="CNPQ" id="cnpq" target="_blank"></a></li>
          <li><a href="http://ccsl.ime.usp.br/" title="CCSL" id="ccsl" target="_blank"></a></li>
          <!--<li><a href="/18/chancela" title="Chancela do Ministério da Cultura" id="chancela" ></a></li>-->
		  <li><a href="{{ URL::to("/") }}/chancela" title="Chancela do Ministério da Cultura" id="chancela"></a></li>
        </ul>
        <ul>
          <li><a href="http://www.usp.br/fau/" title="FAU" id="fau" target="_blank"></a></li>
          <li><a href="http://www.ime.usp.br/" title="IME" id="ime" target="_blank"></a></li>
          <li><a href="http://www.eca.usp.br/" title="ECA" id="eca" target="_blank"></a></li>
        </ul>
        <ul>
          <li><a href="http://www.quapa.fau.usp.br/quapa_desenv/default.htm" title="QUAPÁ" id="quapa" target="_blank"></a></li>
          <li><a href="http://www.vitruvius.com.br/" title="Vitruvius" id="vitruvius" target="_blank"></a></li>
          <li><a href="http://www.archdaily.com.br/br" title="ArchDaily Brasil" id="archdaily" target="_blank"></a></li>
        </ul>
        <ul class="last">
          <li><a href="http://www.bench.com.br/" title="Benchmark" id="benchmark" target="_blank"></a></li>
          <li><a href="http://www.brzcomunicacao.com.br/" title="BRZ" id="brz" target="_blank"></a></li>
          <li><a href="http://doctela.com.br/" title="Doctela" id="doctela" target="_blank"></a></li>	
        </ul>
      </div>
      <!--   FIM - CRÉDITOS - LOGOS   -->
      
      <div class="twelve columns alpha omega">
        <p><small>O Arquigrafia tem envidado todos os esforços para que nenhum direito autoral seja violado. Todas as imagens passíveis de download no Arquigrafia possuem uma licença <a href="http://creativecommons.org/licenses/?lang=pt" target="_blank">Creative Commons</a> específica. Caso seja encontrado algum arquivo/imagem que, por qualquer motivo, o autor entenda que afete seus direitos autorais, <a href="mailto: arquigrafiabrasil@gmail.com">clique aqui</a> e informe à equipe do portal Arquigrafia para que a situação seja imediatamente regularizada.</small></p>
      </div>
    
      <div class="footer-last">
        <div class="footer-msg left">
          <div class="footer-logo"></div>
          <p>O Arquigrafia conta com um total de {{ $count }} fotos.<br />
          <?php if (!Auth::check()) { ?>
            <a href="{{ URL::to("/users/login") }}">Faça o login</a> e compartilhe também suas imagens.
          <?php } else { ?>
            Compartilhe também suas imagens.
          <?php } ?>
          </p>
        </div>
        
        <p id="copyright">Arquigrafia - {{ date("Y") }} - Arquigrafia é uma marca registrada (INPI). Este site possui uma licença <a href="http://creativecommons.org/licenses/by/3.0/deed.pt_BR" target="_blank">Creative Commons Attribution 3.0</a></p>
      
      </div>
    
    </div>

    <!-- begin usabilla live embed code -->
<!--<script type="text/javascript">/*{literal}<![CDATA[*/window.lightningjs||function(c){function g(b,d){d&&(d+=(/\?/.test(d)?"&":"?")+"lv=1");c[b]||function(){var i=window,h=document,j=b,g=h.location.protocol,l="load",k=0;(function(){function b(){a.P(l);a.w=1;c[j]("_load")}c[j]=function(){function m(){m.id=e;return c[j].apply(m,arguments)}var b,e=++k;b=this&&this!=i?this.id||0:0;(a.s=a.s||[]).push([e,b,arguments]);m.then=function(b,c,h){var d=a.fh[e]=a.fh[e]||[],j=a.eh[e]=a.eh[e]||[],f=a.ph[e]=a.ph[e]||[];b&&d.push(b);c&&j.push(c);h&&f.push(h);return m};return m};var a=c[j]._={};a.fh={};a.eh={};a.ph={};a.l=d?d.replace(/^\/\//,(g=="https:"?g:"http:")+"//"):d;a.p={0:+new Date};a.P=function(b){a.p[b]=new Date-a.p[0]};a.w&&b();i.addEventListener?i.addEventListener(l,b,!1):i.attachEvent("on"+l,b);var q=function(){function b(){return["<head></head><",c,' onload="var d=',n,";d.getElementsByTagName('head')[0].",d,"(d.",g,"('script')).",i,"='",a.l,"'\"></",c,">"].join("")}var c="body",e=h[c];if(!e)return setTimeout(q,100);a.P(1);var d="appendChild",g="createElement",i="src",k=h[g]("div"),l=k[d](h[g]("div")),f=h[g]("iframe"),n="document",p;k.style.display="none";e.insertBefore(k,e.firstChild).id=o+"-"+j;f.frameBorder="0";f.id=o+"-frame-"+j;/MSIE[ ]+6/.test(navigator.userAgent)&&(f[i]="javascript:false");f.allowTransparency="true";l[d](f);try{f.contentWindow[n].open()}catch(s){a.domain=h.domain,p="javascript:var d="+n+".open();d.domain='"+h.domain+"';",f[i]=p+"void(0);"}try{var r=f.contentWindow[n];r.write(b());r.close()}catch(t){f[i]=p+'d.write("'+b().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}a.P(2)};a.l&&setTimeout(q,0)})()}();c[b].lv="1";return c[b]}var o="lightningjs",k=window[o]=g(o);k.require=g;k.modules=c}({});
window.usabilla_live = lightningjs.require("usabilla_live", "//w.usabilla.com/0d601cd6875b.js");
/*]]>{/literal}*/</script> 
<!-- end usabilla live embed code -->
    
	</div>
  <!--   FIM - FUNDO DO SITE   -->