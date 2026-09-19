<div class="container container-narrow" style="padding-top:60px; padding-bottom:60px;">
  <div class="card text-center" style="padding:60px 40px; position:relative; overflow:hidden;">
    <div style="position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg, #ef4444, #f59e0b, #ef4444);"></div>
    <div style="font-size:80px; font-weight:900; color:#ef4444; line-height:1; margin-bottom:8px; font-family:'Bebas Neue',sans-serif; letter-spacing:4px; animation:fadeInUp .6s ease both;">500</div>
    <div style="width:60px; height:3px; background:#ef4444; margin:0 auto 20px; border-radius:2px; animation:fadeInUp .6s ease .1s both;"></div>
    <h1 style="font-size:24px; margin-bottom:12px; animation:fadeInUp .6s ease .2s both;">Algo falló de nuestro lado</h1>
    <p style="color:var(--bone-dim); font-size:15px; max-width:420px; margin:0 auto 12px; animation:fadeInUp .6s ease .3s both;">El servidor no pudo completar la solicitud.</p>
    <p style="color:var(--bone-dim); font-size:13px; margin-bottom:30px; animation:fadeInUp .6s ease .35s both;">Intenta de nuevo en unos minutos.</p>
    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; animation:fadeInUp .6s ease .4s both;">
      <button type="button" onclick="location.reload()" class="btn-glow" style="animation:fadeInUp .6s ease .45s both;">
        <i class="fa-solid fa-rotate-right"></i> Reintentar
      </button>
      <a href="<?= BASE_URL ?>index.php?action=home" class="btn-outline" style="animation:fadeInUp .6s ease .5s both;">
        <i class="fa-solid fa-house"></i> Volver al inicio
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
