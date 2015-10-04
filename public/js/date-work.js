 


function retrieveCentury(century){
    //alert(century);
    $('#century').val(century);

}

function retrieveDecade(decade){
    //alert(century);
    $('#decade_select').val(decade);

}


 $(function() {
    
    /*$( "#workDate" )
      .selectmenu()
      .selectmenu( "menuWidget" )
        .addClass( "overflow" );
     $( "#country" )
      .selectmenu()
      .selectmenu( "menuWidget" )
        .addClass( "overflow" );   
     $( "#state" )
      .selectmenu()
      .selectmenu( "menuWidget" )
        .addClass( "overflow" );   */    
  });

 function close_other_date(id) {
       var e = document.getElementById(id);
       
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';

      var century = $('#century').val();
      var decade = $('#decade_select').val();

      if(century != "NS" && century != "Before"){
        century = "Seculo: "+century;
      }else if(century == "Before"){
        century = "Antes do século XV";
      }else{
        century = "";
      }

      if(century != "" && decade != ""){
        decade = "e Década: "+decade;
      }else if(century == "" && (decade != "")){
        decade = "Década: "+decade;
      }else{
        decade = "";
      }

      //alert('cent'+century+'dec'+decade);
      $("#answer_date").text(century+" "+decade);
     }

var period = {
        'Before':['Anterior ao ano de 1401'],
        'XV': ['De 1401 a 1500'],
        'XVI': ['De 1501 a 1600'],
        'XVII': ['De 1601 a 1700'],
        'XVIII': ['De 1701 a 1800'],
        'XIX': ['De 1801 a 1900'],
        'XX': ['De 1901 a 2000'],
        'XXI': ['De 2001 a 2100'],
    }
    var decade ={
        'NS':  ['Escolha década','1401 a 1410','1411 a 1420','1421 a 1430','1431 a 1440','1441 a 1450',
                '1451 a 1460','1461 a 1470','1471 a 1480','1481 a 1490','1491 a 1500',
                '1501 a 1510','1511 a 1520','1521 a 1530','1531 a 1540','1541 a 1550',
                '1551 a 1560','1561 a 1570','1571 a 1580','1581 a 1590','1591 a 1600',
                '1601 a 1610','1611 a 1620','1621 a 1630','1631 a 1640','1641 a 1650',
                '1651 a 1660','1661 a 1670','1671 a 1680','1681 a 1690','1691 a 1700',    
                '1701 a 1710','1711 a 1720','1721 a 1730','1731 a 1740','1741 a 1750',
                '1751 a 1760','1761 a 1770','1771 a 1780','1781 a 1790','1791 a 1800',
                '1801 a 1810','1811 a 1820','1821 a 1830','1831 a 1840','1841 a 1850',
                '1851 a 1860','1861 a 1870','1871 a 1880','1881 a 1890','1891 a 1900',
                '1901 a 1910','1911 a 1920','1921 a 1930','1931 a 1940','1941 a 1950',
                '1951 a 1960','1961 a 1970','1971 a 1980','1981 a 1990','1991 a 2000',     
                '2001 a 2010','2011 a 2020','2021 a 2030','2031 a 2040','2041 a 2050',
                '2051 a 2060','2061 a 2070','2071 a 2080','2081 a 2090','2091 a 2100'],    
        'Before': ['Anterior aos anos 1401'],        
        'XV': ['Escolha década','1401 a 1410','1411 a 1420','1421 a 1430','1431 a 1440','1441 a 1450',
                '1451 a 1460','1461 a 1470','1471 a 1480','1481 a 1490','1491 a 1500'],
        'XVI': ['Escolha década','1501 a 1510','1511 a 1520','1521 a 1530','1531 a 1540','1541 a 1550',
                '1551 a 1560','1561 a 1570','1571 a 1580','1581 a 1590','1591 a 1600'],
        'XVII': ['Escolha década','1601 a 1610','1611 a 1620','1621 a 1630','1631 a 1640','1641 a 1650',
                '1651 a 1660','1661 a 1670','1671 a 1680','1681 a 1690','1691 a 1700'],    
        'XVIII': ['Escolha década','1701 a 1710','1711 a 1720','1721 a 1730','1731 a 1740','1741 a 1750',
                '1751 a 1760','1761 a 1770','1771 a 1780','1781 a 1790','1791 a 1800'],
        'XIX': ['Escolha década','1801 a 1810','1811 a 1820','1821 a 1830','1831 a 1840','1841 a 1850',
                '1851 a 1860','1861 a 1870','1871 a 1880','1881 a 1890','1891 a 1900'],  
        'XX': ['Escolha década','1901 a 1910','1911 a 1920','1921 a 1930','1931 a 1940','1941 a 1950',
                '1951 a 1960','1961 a 1970','1971 a 1980','1981 a 1990','1991 a 2000'],      
        'XXI': ['Escolha década','2001 a 2010','2011 a 2020','2021 a 2030','2031 a 2040','2041 a 2050',
                '2051 a 2060','2061 a 2070','2071 a 2080','2081 a 2090','2091 a 2100'],   
    }

 function showPeriodCentury(century){
    var period_century = period[century];
    if(century != "NS"){
            $("#period_select").text("Periodo: "+period_century);
        }else{
            $("#period_select").text("");
        } 
 }


jQuery(function($) {
    
    
    //alert("ffff");
    var $period = $('#period_select');
    var $decade = $('#decade_select');
    var txtPeriod = '';

//ssdfsdfsdf
    /* var period_century = period[centuryInput] //|| []; 
        alert(period_century);

        if(century != "NS"){
            $("#period_select").text("Periodo: "+period_century);
        } */

    
    $('#century').change(function () {
        var century = $(this).val(), lcns = period[century] //|| []; 
        //alert(century);
        var decadeRange = decade[century]|| [];
        //alert('-lcns='+lcns+'arra'+lcns[0]+'decadR='+decadeRange);
        if(century != "NS"){
            $("#period_select").text("Periodo: "+lcns);
        }else{
            $("#period_select").text("");
        }       

        var i=0;
        var html = $.map(decadeRange, function(decRange){    
        i++;
        if(i==1){
            txtDecRange = '<option value="">' + decRange + '</option>';
            
        }else{
            txtDecRange = '<option value="' + decRange + '">' + decRange + '</option>';
        }
        
        return txtDecRange; 

        }).join('');
        $decade.html(html)
    });





});