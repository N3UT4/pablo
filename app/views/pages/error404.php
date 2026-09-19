<div class="container container-narrow" style="padding-top:60px; padding-bottom:60px;">
  <div class="card text-center" style="padding:60px 40px; position:relative; overflow:hidden;">
    <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg, var(--blood-bright), var(--gold), var(--bone));"></div>
    <div style="font-size:80px; font-weight:900; color:var(--blood-bright); line-height:1; margin-bottom:8px; font-family:'Bebas Neue',sans-serif; letter-spacing:4px; animation:fadeInUp .6s ease both;">404</div>
    <div style="width:60px; height:3px; background:var(--blood-bright); margin:0 auto 20px; border-radius:2px; animation:fadeInUp .6s ease .1s both;"></div>
    <h1 style="font-size:24px; margin-bottom:12px; animation:fadeInUp .6s ease .2s both;">Página no encontrada</h1>
    <p style="color:var(--bone-dim); font-size:15px; max-width:400px; margin:0 auto 30px; animation:fadeInUp .6s ease .3s both;">La ruta que intentas abrir no existe o ya no está disponible.</p>
    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; animation:fadeInUp .6s ease .4s both;">
      <a href="<?= BASE_URL ?>index.php?action=home" class="btn-glow" style="animation:fadeInUp .6s ease .5s both;">
        <i class="fa-solid fa-house"></i> Volver al inicio
      </a>
      <a href="javascript:history.back()" class="btn-outline" style="animation:fadeInUp .6s ease .55s both;">
        <i class="fa-solid fa-arrow-left"></i> Regresar
      </a>
    </div>
  </div>
</div>
<style>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
