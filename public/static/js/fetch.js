const numero = document.querySelector('#cni');
const nom = document.querySelector('#nom');
const prenom = document.querySelector('#prenom');
const adresse = document.querySelector('#adresse');

document.addEventListener("DOMContentLoaded", () => {
    const numero = document.getElementById("cni");

    if (numero) {
        console.log("ok");
        numero.addEventListener("keyup", () => {
            if (numero.value.length === 13) {
                const value = numero.value;
                console.log(value);

                fetch(`https://appdaf-production.up.railway.app/api/cni?nci=${encodeURIComponent(value)}`)
                    .then(res => {
                        if (!res.ok) throw new Error("Network response was not ok");
                        return res.json();
                    })
                    .then(data => {
                        console.log(data);
                        nom.value = data.nom;
                        prenom.value = data.prenom;
                        adresse.value = data.adresse;

                    })
                    .catch(error => console.error("Fetch error:", error));
            }
        });
    } else {
        console.log("Élément #cni introuvable !");
    }
});