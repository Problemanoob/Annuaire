<?php
error_reporting(0);
// connect to Active Directory
$ldap_server = "ldap://controleur-ad-1.cg32.local";
$auth_user = "servicewordpressADT";
$auth_pass = "Bonjour!2023!Bonjour";
if (!($connect=@ldap_connect($ldap_server))) {
    die("Could not connect to LDAP server.");
}
// bind to Active Directory
ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
if (!($bind=@ldap_bind($connect, $auth_user, $auth_pass))) {
    die("Could not bind to LDAP server.");
}
// search Active Directory
$base_dn = "OU=CD32,OU=UTILISATEURS,OU=CD32,DC=cg32,DC=local";
$filter = "(&(|(objectClass=contact)(objectClass=user))(givenName=*)(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(userAccountControl:1.2.840.113556.1.4.803:=2))(!(mail=test@testmail.com)))";
if (!($search=@ldap_search($connect,$base_dn,$filter))) {
    die("Could not search LDAP server.");
}
$count = ldap_count_entries($connect,$search);
$info = ldap_get_entries($connect, $search);
for ($i=0; $i<$count; $i++) {
    $ldap_entries[$i]["sid"]                = $info[$i]["sid"][0];
    $ldap_entries[$i]["sn"]                 = $info[$i]["sn"][0];
    $ldap_entries[$i]["givenname"]          = $info[$i]["givenname"][0];
    $ldap_entries[$i]["mail"]               = $info[$i]["mail"][0];
    $ldap_entries[$i]["extensionattribute9"]    = $info[$i]["extensionattribute9"][0];
    $ldap_entries[$i]["department"]         = $info[$i]["department"][0];
    $ldap_entries[$i]["extensionattribute10"]         = $info[$i]["extensionattribute10"][0];
    $ldap_entries[$i]["extensionattribute11"]         = $info[$i]["extensionattribute11"][0];
    $ldap_entries[$i]["extensionattribute12"]         = $info[$i]["extensionattribute12"][0];
    $ldap_entries[$i]["extensionattribute13"]         = $info[$i]["extensionattribute13"][0];
    $ldap_entries[$i]["title"]         = $info[$i]["title"][0];
    $ldap_entries[$i]["description"]         = $info[$i]["description"][0];
    $ldap_entries[$i]["thumbnailphoto"]         = $info[$i]["thumbnailphoto"][0];
}
usort($ldap_entries, 'sortuser'); 
?>
<!DOCTYPE html>
<html>
<head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>Annuaire CD32</title>
     <link rel="stylesheet" type="text/css" href="style.css">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>  
     <script src="script.js"></script>
     <link href="images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
     <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
     <div class="row">
          <div class="columnone">
               <img class="logoimage" src="logoimage.png"/>
          </div>
          <div class="columntwo" id="column-content">
               <p align="center"><?php echo "$count" ?> Contacts, Base DN <?php echo "$base_dn" ?> </p>
          </div>
     </div>

</head>
<body>
     <div class="main-content">
          <div class="search-container">
               <div class="search">
                    <span>
                         <input type="text" name="search" id="search" placeholder="Recherche par nom, direction, service, poste, etc..." />
                    </span>
               </div>
               <a class="redirect" href="annuaire_asso.php">Association</a>
               <button id="show_button" type="button" class="adv-search-collapsible" onclick="(showAdvanced())" >Recherche Avancée</button>
               <div class="adv-search-content">
                    
                    <div class="adv-search-form">
                         <form autocomplete="chrome-off">
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=0 name="search_firstname" id="search_firstname"  placeholder="Nom"/>
                              </span>
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=1 name="search_lastname" id="search_lastname"  placeholder="Prénom"/>
                              </span>
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=6 name="search_service" id="search_service"  placeholder="Service"/>
                              </span>
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=5 name="search_direction" id="search_direction" placeholder="Direction" />
                              </span>
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=9 name="search_acronym" id="search_acronym" placeholder="Acronyme DGA/Direction/Service/Pôle..." />
                              </span>
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=8 name="search_title" id="search_title" placeholder="Fonction" />
                              </span>
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=2 name="search_mail" id="search_mail" placeholder="Courriel" />
                              </span>
                              <span class="adv-input-row">
                                   <input class="adv-search-input" type="text" onclick="destroyAllAC()" onkeyup="filterTable(this.dataset.indexNumber)" data-index-number=3 name="search_phone" id="search_phone" placeholder="Téléphone" />
                              </span>
                         </form>
                    </div>
                    <div class="adv-search-footer">
                         <button id="clean_up" disabled=true onclick="cleanAllAdvInputs()">Effacer le formulaire</button> 
                    </div>
               </div>
          </div>
          <br/>
          <div class="result-container">
               <table id="employee_table" class="order-table table styled-table">
                    <thead>
                         <tr id="table-raw">
                         <th role="columnheader">Nom</th>
                         <th role="columnheader">Prénom</th>
                         <th role="columnheader">E-Mail</th>
                         <th role="columnheader">Téléphone</th>
                         <th role="columnheader">DGA</th>
                         <th role="columnheader">Direction</th>
                         <th role="columnheader">Service</th>
                         <th role="columnheader">Pole</th>
                         <th role="columnheader">Fonction</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php for ($i = 1; $i < $count; $i++) { $j = 1; ?>
                         <tr role="row" id="row" class='clickable-row' onclick="showDetails(this)"> 
                              <td id="col" role="cell"><?php echo $ldap_entries[$i]["sn"];?></td>
                              <td id="col" role="cell"><?php echo $ldap_entries[$i]["givenname"];?></td>
                              <td id="col" role="cell"><a href="mailto:<?php echo UTF8_decode($ldap_entries[$i]["mail"]);?>"><?php echo $ldap_entries[$i]["mail"];?></a></td>
                              <td id="col" role="cell"><a href="callto:<?php echo UTF8_decode($ldap_entries[$i]["extensionattribute9"]);?>"><?php echo UTF8_decode($ldap_entries[$i]["extensionattribute9"]);?></a></td>
                              <td id="col" role="cell"><?php echo $ldap_entries[$i]["extensionattribute10"];?></td>
                              <td id="col" role="cell"><?php echo $ldap_entries[$i]["extensionattribute11"];?></td>
                              <td id="col" role="cell"><?php echo $ldap_entries[$i]["extensionattribute12"];?></td>
                              <td id="col" role="cell"><?php echo $ldap_entries[$i]["extensionattribute13"];?></td>
                              <td id="col" role="cell"><?php echo $ldap_entries[$i]["title"];?></td>
                              <td id="col" role="cell" class="search-criteria"><?php echo UTF8_decode($ldap_entries[$i]["description"]);?></td>
                              <td class="search-criteria"><img src="data:image/jpeg;base64,<?php echo base64_encode($ldap_entries[$i]["thumbnailphoto"]); ?>"/></td>
                         </tr>
                              <?php } 
                              function sortuser($mod_a, $mod_b){
                              // Sort by Lastname
                              $a = $mod_a["sn"];
                              $b = $mod_b["sn"];
                              if ($a == $b) {
                                   return 0;
                              }
                              return ($a < $b) ? -1 : +1;
                              }
                              ?>
                    </tbody>
               </table>
          </div>
     </div>
     <!-- The Modal -->
     <div id="myModal" class="modal">
          <!-- Modal content -->
          <div class="modal-content">
               <div class="modal-header" id="data_header"></div>
               <div class="modal-body" id="data_details">
                    <p>Some text in the Modal Body</p>
                    <p>Some other text...</p>
               </div>
               <div class="modal-footer" id="data_others">
                    <h3>Modal Footer</h3>
               </div>
          </div>
     </div>

</body>
</html>
<script>  
     $(document).ready(function(){  
          $('#search').keyup(function(){  
               search_table($(this).val());  
          });

          function search_table(value){  
               $('#employee_table tbody tr').each(function(){  
                    var found = 'false';  
                    $(this).each(function(){  
                         if($(this).text().toLowerCase().indexOf(value.toLowerCase()) >= 0)  
                         {  
                              found = 'true';  
                         }  
                    });  
                    if(found == 'true')  
                    {  
                         $(this).show();  
                    }  
                    else  
                    {  
                         $(this).hide();  
                    }  
               });  
          }

     });

     function filterTable(indexNumber) {
          cleanInputSearch(indexNumber);
          document.getElementById("clean_up").disabled=false;
          document.getElementById("search").disabled=true;
          var advancedInputs = document.querySelectorAll("input.adv-search-input");
          var table = document.getElementById("employee_table");
          var tr = table.getElementsByTagName("tr");
          for (const input of advancedInputs) {
               filter = input.value.toUpperCase();
               for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[input.dataset.indexNumber];
                    if (td) {
                         txtValue = td.textContent || td.innerText;
                         if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                   // Nothing for the moment;
                              } else {
                                   tr[i].style.display = "none";
                              }
                         }
                    } 
          }
          autoCompleteFromColumn(indexNumber);
     }

     function autoCompleteFromColumn(indexNumber) {
          destroyAllAC();       
          var advancedInputs = document.querySelectorAll("input.adv-search-input");
          var arr = [];
          var table = document.getElementById("employee_table");
          var tr = table.getElementsByTagName("tr");

          for (i = 0; i < tr.length; i++) {
               td = tr[i].getElementsByTagName("td")[indexNumber];
               if (td && !tr[i].style.display) {
                    if (!arr.includes(td.innerText)) {
                         arr.push(td.innerText);
                    }
               }
          }

          if(arr.length < 30 && arr.length > 0) {
               var acList = document.createElement('div');
               acList.id = "autocomplete-result";
               for (const input of advancedInputs) {
                    if(input.dataset.indexNumber === indexNumber) {        
                         input.after(acList);
                    }
               }
               showACResults(arr, indexNumber);
          } else {
               destroyAllAC();
          }

     }

     function destroyAllAC(){
          var acList = document.getElementById("autocomplete-result");
          if(acList&&acList.innerHTML) {
               while (acList.firstChild) {
                    acList.removeChild(acList.lastChild);
                    //console.log("Destroy all AC!");
               }
               acList.remove();
          } else {
               // console.log("No AC opened!");
          }
     }

     function showACResults(val, indexNumber) {
          res = document.getElementById("autocomplete-result");
          if(res) {
               res.innerHTML = '';
               let list = '';
               let terms = val;
               for (i=0; i<terms.length; i++) {
                    list += '<li onClick="setInputSearch(this,'+ indexNumber +')">' + terms[i] + '</li>';
               }
               res.innerHTML = '<ul>' + list + '</ul>';
          }
     }

     function setInputSearch(element, indexNumber) {
          var advancedInputs = document.querySelectorAll("input.adv-search-input");
          for (const input of advancedInputs) {
               if(input.dataset.indexNumber == indexNumber) {
                    input.value = element.innerText;
                    filterTable(indexNumber);
               }
          }
     }

     function cleanInputSearch(indexNumber) {
          table = document.getElementById("employee_table");
          tr = table.getElementsByTagName("tr");
          for (i = 0; i < tr.length; i++) {
               td = tr[i].getElementsByTagName("td")[indexNumber];
               if (td) {
                    txtValue = td.textContent || td.innerText;
                    tr[i].style.display = "";
               }
          }
     }
          
     function cleanAllAdvInputs() {
          var advancedInputs = document.querySelectorAll("input.adv-search-input");
          for (const input of advancedInputs) {
               input.value = '';
               cleanInputSearch(input.dataset.indexNumber)
          }
          document.getElementById("clean_up").disabled=true;
     } 

     function cleanBasicInput() {
          document.getElementById("search").value='';
          table = document.getElementById("employee_table");
          tr = table.getElementsByTagName("tr");
          for (i = 0; i < tr.length; i++) {
               td = tr[i].getElementsByTagName("td")[0];
               if (td) {
                    txtValue = td.textContent || td.innerText;
                    tr[i].style.display = "";
               }
          }
     } 

     function showAdvanced() {
          element = document.getElementById("show_button");
          element.classList.toggle("active");
          var content = element.nextElementSibling;
          if (content.style.maxHeight) {
               cleanAllAdvInputs();
               content.style.maxHeight = null;
               document.getElementById("search").disabled=false;
          } else {
               cleanBasicInput();
               content.style.maxHeight = content.scrollHeight + "px";
               document.getElementById("search").disabled=true;
          }               
     }


     function showDetails(tableRow){
          NodeList.prototype.forEach = Array.prototype.forEach
          var children = tableRow.childNodes;
          var arr = [];
          children.forEach(function(item){
               if(item) {
                    if(item.innerText == '' || item.innerText != null) {
                         arrValue = item.innerText;
                         if(item.childNodes[0].nodeName == "IMG" && item.childNodes[0]) {
                              arrValue = item.innerHTML;
                         }
                         arr.push(arrValue);
                    } 
               }               
          })

          //console.log(arr);
          //Fill the modal Header
          dataHeader = document.getElementById("data_header");
          var dHeader = document.createElement('span');
          dHeader.innerHTML = '<span class="close" onclick="closeModal()">&times;</span>'
                         + arr[10] 
                         + '<h2>' + arr[0] + ' ' + arr[1] + '</h2>'
                         + '<p>' + arr[8] + '</p>';
          dataHeader.innerHTML = dHeader.innerHTML;

          //Fill the modal Codetailsntent
          dataDetails = document.getElementById("data_details");
          var dDetails = document.createElement('div');
          dDetails.innerHTML =  '<p><h3>' + arr[4] +'</h2></p>'
                         + '<p>' + arr[5] +'</p>'
                         + '<p>' + arr[6] + '</p>'
                         + '<p>' + arr[7] + '</p>';
          dataDetails.innerHTML = dDetails.innerHTML;
          
          dataFooter = document.getElementById("data_others");
          var dOthers = document.createElement('div');
          dOthers.innerHTML = '<p> Téléphone : <a href="tel:' + arr[3] + '">' + arr[3] +'</a></p>'
                         + '<p> Email : <a href="mailto:' + arr[2] + '">' + arr[2] +'</a></p>';
          dataFooter.innerHTML = dOthers.innerHTML;
          //Display the modal
          modal.style.display = "block";
     }

     function closeModal() {
          modal.style.display = "none";
     }
     // TODO  : implémenter l'optimisation des perfs : manipuler le DOM que si X caractères sont saisis
     // (if length input > multiple de x => recherche)
     // + code cleanup et surtout commentaires !!!!!
     // + debuggage 
     // Séparation des scripts!!

     // Get the modal
     var modal = document.getElementById("myModal");

     // Get the <span> element that closes the modal
     var span = document.getElementsByClassName("close")[0];

     window.onclick = function(event) {
          if (event.target == modal) {
               modal.style.display = "none";
          }
     }

</script>  
