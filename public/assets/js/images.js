let links = document.querySelector("[data-delete]");
console.log(links);
// //on boucle sur les liens
// for (let link of links){
//     //on met une ecoute d evnmt
//     link.addEventListener("click", function(e){
//         //on empeche la  navigation
//         e.preventDefault();
//         //on demande confirmation
//         if (confirm("Voulez vous supprimer cette image ?")) {
//             //on envoie la requete ajax
//             fetch(this.getAttribute("href"),{
//                 method: "DELETE",
//                 headers: {
//                     "X-Requested-with": "XMLHttpRequest",
//                     "Content-Type": "application/json"
//                 },
//                 body: JSON.stringify({"_token": this.dataset.token})
//             }).then(response => response.json())
//             .then(data =>{
//                 if (data.success) {
//                     this.parentElement.remove();
//                 }else{
//                     alert(data.error);
//                 }
//             })
            
//         }
//     });
// }