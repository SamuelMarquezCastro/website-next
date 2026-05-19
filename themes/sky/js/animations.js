(function () {
  const isEditing = document.body.classList.contains("wcms-logged-in") || document.querySelector("#adminPanel");
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (isEditing || reduceMotion || !("IntersectionObserver" in window)) {
    return;
  }

  document.documentElement.classList.add("has-scroll-animations");

  const revealGroups = [
    ".section",
    ".quote-band",
    ".pattern-band",
    ".hero__kicker",
    ".hero h1",
    ".page-hero h1",
    ".page-hero p",
    ".intro-copy h2",
    ".intro-copy p",
    ".button",
    ".section-title",
    ".eyebrow",
    ".lead",
    ".poster",
    ".hero-art",
    ".vision-pillars-image",
    ".pillar-card",
    ".audience-card",
    ".value-card",
    ".contact-card",
    ".activity-card",
    ".timeline-step",
    ".about-item",
    ".team-member",
    ".map-frame",
    ".footer-mark",
    ".footer-col",
  ];

  const staggerContainers = [
    [".pillar-grid", ".pillar-card"],
    [".audience-grid", ".audience-card"],
    [".value-grid", ".value-card"],
    [".contact-grid", ".contact-card"],
    [".activity-grid", ".activity-card"],
    [".timeline", ".timeline-step"],
    [".about-list", ".about-item"],
    [".team-list", ".team-member"],
  ];

  const revealItems = new Set();

  revealGroups.forEach((selector) => {
    document.querySelectorAll(selector).forEach((element) => revealItems.add(element));
  });

  staggerContainers.forEach(([containerSelector, childSelector]) => {
    document.querySelectorAll(containerSelector).forEach((container) => {
      container.querySelectorAll(childSelector).forEach((element, index) => {
        element.style.setProperty("--reveal-delay", `${Math.min(index * 90, 540)}ms`);
      });
    });
  });

  revealItems.forEach((element) => {
    element.classList.add("reveal-on-scroll");
  });

  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    },
    {
      rootMargin: "0px 0px -12% 0px",
      threshold: 0.14,
    }
  );

  revealItems.forEach((element) => revealObserver.observe(element));

  const parallaxItems = Array.from(
    document.querySelectorAll(".poster, .hero-art, .decor, .activity-card__triangle, .footer-mark img")
  );

  if (!parallaxItems.length) {
    return;
  }

  let ticking = false;

  function updateParallax() {
    const viewportHeight = window.innerHeight || 1;

    parallaxItems.forEach((element, index) => {
      const rect = element.getBoundingClientRect();

      if (rect.bottom < -120 || rect.top > viewportHeight + 120) {
        return;
      }

      const progress = (rect.top + rect.height / 2 - viewportHeight / 2) / viewportHeight;
      const strength = element.classList.contains("decor") ? 34 : 18;
      const direction = index % 2 === 0 ? -1 : 1;
      const y = progress * strength * direction;
      const rotate = element.classList.contains("activity-card__triangle") ? progress * 4 : 0;

      element.style.setProperty("--parallax-y", `${y.toFixed(2)}px`);
      element.style.setProperty("--parallax-rotate", `${rotate.toFixed(2)}deg`);
    });

    ticking = false;
  }

  function requestParallaxUpdate() {
    if (ticking) {
      return;
    }

    ticking = true;
    window.requestAnimationFrame(updateParallax);
  }

  updateParallax();
  window.addEventListener("scroll", requestParallaxUpdate, { passive: true });
  window.addEventListener("resize", requestParallaxUpdate);
})();
