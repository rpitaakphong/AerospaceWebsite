const menuButton = document.querySelector('.menu-toggle');
const menu = document.querySelector('#primary-nav');
document.querySelectorAll('.hero-mosaic__tile img').forEach((image) => {
  const markUnavailable = () => image.closest('.hero-mosaic__tile').classList.add('is-unavailable');
  image.addEventListener('error', markUnavailable, {once: true});
  if (image.complete && image.naturalWidth === 0) markUnavailable();
});

menuButton?.addEventListener('click', () => {
  const isOpen = menu.classList.toggle('is-open');
  menuButton.setAttribute('aria-expanded', String(isOpen));
  menuButton.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
});

menu?.addEventListener('click', (event) => {
  if (event.target.closest('a') && menu.classList.contains('is-open')) {
    menu.classList.remove('is-open');
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Open menu');
  }
});

function plainText(markup) {
  return new DOMParser().parseFromString(markup || '', 'text/html').body.textContent.trim();
}

function siteUrl(value) {
  try {
    const url = new URL(value);
    return url.hostname === 'aero.ise.eng.chula.ac.th' && url.protocol === 'https:' ? url.href : null;
  } catch {
    return null;
  }
}

function makeNewsCard(post) {
  const link = siteUrl(post.link);
  const media = post._embedded?.['wp:featuredmedia']?.[0];
  const image = siteUrl(media?.media_details?.sizes?.large?.source_url || media?.media_details?.sizes?.medium_large?.source_url || media?.source_url || media?.media_details?.sizes?.medium?.source_url);
  const title = plainText(post.title?.rendered);
  if (!link || !image || !title || !post.date) return null;

  const card = document.createElement('a');
  card.className = 'news-card';
  card.href = link;

  const imageBox = document.createElement('div');
  imageBox.className = 'news-card__image';
  const img = document.createElement('img');
  img.src = image;
  img.alt = '';
  img.loading = 'lazy';
  imageBox.append(img);

  const body = document.createElement('div');
  body.className = 'news-card__body';
  const label = document.createElement('span');
  label.className = 'small-label';
  const date = new Date(`${post.date.slice(0, 10)}T12:00:00Z`);
  label.textContent = `NEWS / ${new Intl.DateTimeFormat('en-GB', {day: '2-digit', month: 'short', year: 'numeric', timeZone: 'UTC'}).format(date).toUpperCase()}`;
  const heading = document.createElement('h3');
  heading.textContent = title;
  const read = document.createElement('span');
  read.className = 'card-arrow';
  read.textContent = 'Read story ';
  const arrow = document.createElement('span');
  arrow.setAttribute('aria-hidden', 'true');
  arrow.textContent = '↗';
  read.append(arrow);
  body.append(label, heading, read);
  card.append(imageBox, body);
  return card;
}

async function refreshNews() {
  const grid = document.querySelector('#news-grid');
  if (!grid) return;

  try {
    const url = 'https://aero.ise.eng.chula.ac.th/wp-json/wp/v2/posts?categories=14&per_page=3&orderby=date&order=desc&_embed=wp:featuredmedia';
    const response = await fetch(url, {cache: 'no-store'});
    if (!response.ok) throw new Error(`News request returned ${response.status}`);
    const posts = await response.json();
    if (!Array.isArray(posts) || posts.length !== 3) throw new Error('The News feed did not return three posts');
    const cards = posts.map(makeNewsCard);
    if (cards.some((card) => !card)) throw new Error('A News post is missing its featured image or link');
    grid.replaceChildren(...cards);
    grid.dataset.source = 'wordpress-news-category';
  } catch (error) {
    // The preview remains useful offline; its initial three cards are the most recent known News posts.
    grid.dataset.source = 'fallback';
    console.info('Showing saved News preview cards:', error.message);
  }
}

refreshNews();
