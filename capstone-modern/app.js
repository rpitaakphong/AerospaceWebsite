const grid = document.querySelector('#project-grid');
const search = document.querySelector('#project-search');
const filters = [...document.querySelectorAll('.filter')];
const yearTabs = [...document.querySelectorAll('.year-tab')];
const resultCount = document.querySelector('#result-count');
const emptyState = document.querySelector('#empty-state');
const clearSearch = document.querySelector('#clear-search');
let projects = [];
let activeFilter = 'All';
let activeYear = 'All';

const imageById = {
  emergensea: 'assets/emergensea-cover-v2.webp',
  marslex: 'assets/marslex-cover-v2.webp',
  shadow: 'assets/shadow-cover-v2.webp',
  'survi-drone': 'assets/survi-drone-cover-v2.webp',
  'first-response': 'assets/first-response-cover-v2.webp'
};

function escapeHTML(value = '') {
  return String(value).replace(/[&<>'"]/g, character => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
  })[character]);
}

function missionDomain(project) {
  return /space|mars|rocket|orbital/i.test(`${project.category} ${project.title} ${project.subtitle}`) ? 'Space systems' : 'Earth systems';
}

function evidencePages(project) {
  if (project.pages) return `pages ${project.pages}`;
  const pages = [...new Set((project.evidence || []).map(item => item.reportPage || item.pdfPage).filter(Boolean))];
  return pages.length ? `evidence pages ${pages.join(', ')}` : 'project report';
}

function projectCard(project, index) {
  const domain = missionDomain(project);
  const searchText = [project.year, project.name, project.category, domain, project.title, project.subtitle, project.summary, project.approach, project.results, project.next, (project.team || []).join(' '), project.advisor, project.coAdvisor].join(' ').toLowerCase();
  const metrics = project.metrics.map(([value, label]) => `<div class="metric"><strong>${escapeHTML(value)}</strong><span>${escapeHTML(label)}</span></div>`).join('');
  const advising = [project.advisor, project.coAdvisor].filter(Boolean).map(escapeHTML).join(' · ');
  return `<article class="project-card" data-year="${project.year}" data-domain="${escapeHTML(domain)}" data-search="${escapeHTML(searchText)}" id="${escapeHTML(project.id)}">
    <div class="project-index"><b>${String(index + 1).padStart(2, '0')}</b><span>MISSION / AY${String(project.year).slice(-2)}</span></div>
    <div class="project-copy">
      <p class="domain">${escapeHTML(project.category)} / ${project.year}</p>
      <h3>${escapeHTML(project.name)}</h3>
      <p class="project-title">${escapeHTML(project.title)}</p>
      <p class="project-summary">${escapeHTML(project.summary)}</p>
      <p class="project-status">${escapeHTML(project.status)}</p>
    </div>
    <div class="project-visual">
      <div class="image-stage"><img src="${escapeHTML(project.image)}" alt="${escapeHTML(project.caption)}" loading="lazy"></div>
      <div class="metrics">${metrics}</div>
    </div>
    <details class="project-details">
      <summary>Open engineering record <span aria-hidden="true">+</span></summary>
      <div class="detail-panel">
        <section><h4>Design approach</h4><p>${escapeHTML(project.approach)}</p></section>
        <section><h4>Reported results</h4><p>${escapeHTML(project.results)}</p></section>
        <section><h4>Next validation step</h4><p>${escapeHTML(project.next)}</p></section>
        <section><h4>Mission statement</h4><p>${escapeHTML(project.subtitle)}</p></section>
        <div class="people-line">
          <section><h4>Student engineering team</h4><p class="team-list">${(project.team || []).map(escapeHTML).join(' · ')}</p></section>
          <section><h4>Advising</h4><p>${advising}</p></section>
        </div>
        <p class="source-note">Source: ${escapeHTML(project.source)}, ${escapeHTML(evidencePages(project))}. Images and reported results are drawn from the student design report.</p>
      </div>
    </details>
  </article>`;
}

function updateView() {
  const query = search.value.trim().toLowerCase();
  let visible = 0;
  document.querySelectorAll('.project-card').forEach(card => {
    const matchesYear = activeYear === 'All' || card.dataset.year === activeYear;
    const matchesCategory = activeFilter === 'All' || card.dataset.domain === activeFilter;
    const matchesSearch = !query || card.dataset.search.includes(query);
    const show = matchesYear && matchesCategory && matchesSearch;
    card.hidden = !show;
    if (show) visible += 1;
  });
  resultCount.textContent = String(visible).padStart(2, '0');
  emptyState.hidden = visible !== 0;

  document.querySelectorAll('[data-domain-count]').forEach(counter => {
    const domain = counter.dataset.domainCount;
    const count = projects.filter(project => (activeYear === 'All' || String(project.year) === activeYear) && (domain === 'All' || missionDomain(project) === domain)).length;
    counter.textContent = String(count).padStart(2, '0');
  });
}

filters.forEach(button => button.addEventListener('click', () => {
  activeFilter = button.dataset.filter;
  filters.forEach(item => {
    const active = item === button;
    item.classList.toggle('is-active', active);
    item.setAttribute('aria-pressed', String(active));
  });
  updateView();
}));

yearTabs.forEach(button => button.addEventListener('click', () => {
  activeYear = button.dataset.year;
  yearTabs.forEach(item => {
    const active = item === button;
    item.classList.toggle('is-active', active);
    item.setAttribute('aria-pressed', String(active));
  });
  updateView();
}));

search.addEventListener('input', updateView);
clearSearch.addEventListener('click', () => {
  activeFilter = 'All';
  activeYear = 'All';
  search.value = '';
  filters[0].click();
  yearTabs[0].click();
  search.focus();
});

window.addEventListener('beforeprint', () => document.querySelectorAll('details').forEach(item => {
  item.dataset.prePrintOpen = String(item.open);
  item.open = true;
}));
window.addEventListener('afterprint', () => document.querySelectorAll('details').forEach(item => {
  item.open = item.dataset.prePrintOpen === 'true';
}));

Promise.all([
  fetch('projects.json?ver=1.0.10').then(response => {
    if (!response.ok) throw new Error(`2025 project data returned ${response.status}`);
    return response.json();
  }).then(data => data.map(project => ({ ...project, year: 2025, image: imageById[project.id] }))),
  fetch('data/2024.json?ver=1.0.10').then(response => {
    if (!response.ok) throw new Error(`2024 project data returned ${response.status}`);
    return response.json();
  }).then(data => data.map(project => ({ ...project, image: `assets/2024/${project.image.split('/').pop()}` }))),
  fetch('data/2023.json?ver=1.0.10').then(response => {
    if (!response.ok) throw new Error(`2023 project data returned ${response.status}`);
    return response.json();
  }).then(data => data.map(project => ({ ...project, image: `assets/2023/${project.image.split('/').pop()}` })))
])
  .then(dataByYear => {
    const featuredOrder = {
      2025: { marslex: 0, shadow: 1 },
      2024: { 'skyreach-horizon': 0 },
      2023: { stars: 0 }
    };
    projects = dataByYear.flat().sort((a, b) => {
      const aPriority = featuredOrder[a.year]?.[a.id] ?? Number.MAX_SAFE_INTEGER;
      const bPriority = featuredOrder[b.year]?.[b.id] ?? Number.MAX_SAFE_INTEGER;
      return b.year - a.year || aPriority - bPriority || a.name.localeCompare(b.name);
    });
    grid.innerHTML = projects.map(projectCard).join('');
    document.querySelector('#archive-project-count').textContent = String(projects.length).padStart(2, '0');
    document.querySelector('#archive-student-count').textContent = String(projects.reduce((total, project) => total + (project.team || []).length, 0)).padStart(2, '0');
    document.querySelectorAll('[data-year-count]').forEach(counter => {
      const year = counter.dataset.yearCount;
      const count = projects.filter(project => year === 'All' || String(project.year) === year).length;
      counter.textContent = String(count).padStart(2, '0');
    });
    updateView();
  })
  .catch(error => {
    console.error(error);
    grid.innerHTML = '<div class="empty-state"><span>DATA OFFLINE</span><h3>The project archive could not be loaded.</h3><p>Run this prototype from a local web server rather than opening the file directly.</p></div>';
  });
