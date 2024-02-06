function carregar(){
    var msg = window.document.getElementById('msg')
    var img = document.getElementById('imagem')
    var data = new Date()
    var hora = data.getHours()
    
    msg.innerHTML = `Agora são ${hora} horas.`
    if (hora < 12){
        //Bom Dia
        img.src='manha.png'
        document.body.style.background = '#dec3a6'
    } else if (hora >= 12 && hora < 18) {
        //Boa Tarde
        img.src='tarde.png'
        document.body.style.background = '#c37d6e'
    } else {
        //boa Noite
        img.src='noite.png'
        document.body.style.background = '#588ebb'
    }
}

