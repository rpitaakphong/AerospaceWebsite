document.querySelectorAll('.aero-home .hero-mosaic__tile img').forEach((image) => {
  const unavailable = () => image.closest('.hero-mosaic__tile').classList.add('is-unavailable');
  image.addEventListener('error', unavailable, {once: true});
  if (image.complete && image.naturalWidth === 0) unavailable();
});
