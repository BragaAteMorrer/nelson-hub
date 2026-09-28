function tick(){
  const el=document.querySelector('#clock');
  if(el){
    el.textContent=new Intl.DateTimeFormat('fr-FR',{
      dateStyle:'full',
      timeStyle:'short',
      timeZone:'Europe/Paris'
    }).format(new Date());
  }
}
tick();
setInterval(tick,30000);
if('serviceWorker' in navigator){
  navigator.serviceWorker.register('./sw.js').catch(()=>{});
}
