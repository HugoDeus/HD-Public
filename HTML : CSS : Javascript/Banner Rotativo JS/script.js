/** Banner */
let fotos = ['banner1.png', 'banner2.png', 'banner3.png']

function bannerswitch(ft){
    document.querySelector('.imagem-banner').src = "imagens/" + fotos[ft]
}

let bannertroca = 0

bannerswitch(bannertroca)


let contar = setInterval(function relogio(){
    bannertroca++
    if(bannertroca >= fotos.length){
        bannertroca = 0
    }
    bannerswitch(bannertroca)
}, 4000)

/** Botoes iniciar/parar Banner */

document.getElementById('btiniciar').addEventListener('click', function(){
    contar = setInterval(function relogio(){
        bannertroca++
        if(bannertroca >= fotos.length){
            bannertroca = 0
        }
        bannerswitch(bannertroca)
    }, 4000)
    document.getElementById("btiniciar").disabled = true;
    document.getElementById("btparar").disabled = false;
})

document.getElementById("btparar").addEventListener("click", function() {
    clearInterval(contar);
    document.getElementById("btiniciar").disabled = false;
    document.getElementById("btparar").disabled = true;
});
