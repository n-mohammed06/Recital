/*
 * Récital — petites améliorations des pages.
 * Le site fonctionne aussi sans ce fichier : il ajoute seulement du confort.
 */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Les mêmes règles que le serveur (AppServiceProvider) : 8 caractères, une lettre, un chiffre.
  // Le serveur vérifie en plus que le mot de passe n'est pas apparu dans une fuite de données.
  var RULES = {
    length: function (value) { return Array.from(value).length >= 8; },
    letter: function (value) { return /\p{L}/u.test(value); },
    digit: function (value) { return /\p{N}/u.test(value); }
  };

  /* ---------- 1. Bouton « œil » : afficher ou masquer le mot de passe ---------- */
  function setVisible(field, visible) {
    var input = field.querySelector('input');
    var button = field.querySelector('[data-toggle-password]');
    if (!input || !button) return;

    input.type = visible ? 'text' : 'password';
    button.setAttribute('aria-pressed', visible ? 'true' : 'false');
    // Les icônes sont des SVG : la propriété .hidden n'existe pas sur eux, on passe par l'attribut.
    button.querySelector('[data-icon="show"]').toggleAttribute('hidden', visible);
    button.querySelector('[data-icon="hide"]').toggleAttribute('hidden', !visible);
  }

  function initPasswordToggle(field) {
    var input = field.querySelector('input');
    var button = field.querySelector('[data-toggle-password]');
    if (!input || !button) return;

    button.hidden = false;

    // Sur téléphone, toucher le bouton ne doit pas fermer le clavier : le champ garde le focus.
    button.addEventListener('mousedown', function (event) {
      event.preventDefault();
    });

    button.addEventListener('click', function () {
      var hadFocus = document.activeElement === input;
      var start = input.selectionStart;
      var end = input.selectionEnd;

      setVisible(field, input.type === 'password');

      // Le curseur reste là où il était.
      if (hadFocus && start !== null) {
        try { input.setSelectionRange(start, end); } catch (error) { /* rien à faire */ }
      }
    });
  }

  /* ---------- 2. Règles du mot de passe cochées pendant la saisie ---------- */
  function initPasswordRules(field) {
    var input = field.querySelector('input');
    var list = field.querySelector('[data-rules]');
    if (!input || !list) return;

    var items = list.querySelectorAll('[data-rule]');

    function update() {
      items.forEach(function (item) {
        var check = RULES[item.getAttribute('data-rule')];
        var ok = check ? check(input.value) : false;
        var state = item.querySelector('[data-rule-state]');

        item.classList.toggle('est-ok', ok);
        if (state) state.textContent = ok ? ' : validé' : '';
      });
    }

    input.addEventListener('input', update);
    update();
  }

  /* ---------- 3. Envoi du formulaire ---------- */
  function submitLabel(button) {
    return button.querySelector('.dg-bouton__texte') || button;
  }

  function initForm(form) {
    var button = form.querySelector('[data-submit]');
    if (button) button.setAttribute('data-label', submitLabel(button).textContent);

    form.addEventListener('submit', function (event) {
      // Un deuxième appui pendant l'envoi ne renvoie pas le formulaire.
      if (form.getAttribute('data-sending') === 'true') {
        event.preventDefault();
        return;
      }
      form.setAttribute('data-sending', 'true');

      // Le mot de passe repasse en masqué : le navigateur ne doit pas le retenir comme un texte ordinaire.
      form.querySelectorAll('[data-password-field]').forEach(function (field) {
        setVisible(field, false);
      });

      if (button && button.getAttribute('data-sending-label')) {
        submitLabel(button).textContent = button.getAttribute('data-sending-label');
        button.setAttribute('aria-disabled', 'true');
      }
    });
  }

  function resetForm(form) {
    var button = form.querySelector('[data-submit]');
    form.removeAttribute('data-sending');
    if (button && button.hasAttribute('data-label')) {
      submitLabel(button).textContent = button.getAttribute('data-label');
      button.removeAttribute('aria-disabled');
    }
  }

  /* ---------- 4. Récapitulatif des erreurs ---------- */
  function initErrorSummary(summary) {
    // Placé en tête et annoncé par les lecteurs d'écran dès l'arrivée sur la page.
    summary.focus();

    // Un lien du récapitulatif amène au champ et y place le curseur.
    summary.addEventListener('click', function (event) {
      var link = event.target.closest('a[href^="#"]');
      if (!link) return;

      var target = document.getElementById(link.getAttribute('href').slice(1));
      if (!target) return;

      event.preventDefault();
      (target.closest('.dg-champ, .dg-case') || target).scrollIntoView({ block: 'start' });
      target.focus({ preventScroll: true });
    });
  }

  /* ---------- 5. Slogan qui s'écrit puis s'efface ---------- */
  // Les phrases viennent de config/recital.php. Le texte animé est caché aux lecteurs d'écran,
  // qui lisent la première phrase, fixe. Animations réduites sur le téléphone : la phrase reste fixe.
  function initTypewriter(element) {
    var phrases;
    try { phrases = JSON.parse(element.getAttribute('data-typewriter')); } catch (error) { return; }
    if (!Array.isArray(phrases) || phrases.length < 2 || reduceMotion) return;

    // Un seul défilement à la fois par slogan, même si on relance l'animation.
    var run = (element.recitalRun || 0) + 1;
    element.recitalRun = run;
    element.textContent = phrases[0];

    var index = 0;
    var letters = Array.from(phrases[0]);
    var shown = letters.length;
    var erasing = true;

    function step() {
      if (!element.isConnected || element.recitalRun !== run) return; // page quittée ou animation relancée

      var delay;
      if (erasing) {
        shown--;
        delay = 28;
        if (shown <= 0) {
          shown = 0;
          erasing = false;
          index = (index + 1) % phrases.length;
          letters = Array.from(phrases[index]);
          delay = 380;
        }
      } else {
        shown++;
        delay = 62;
        if (shown >= letters.length) {
          erasing = true;
          delay = 2600; // le temps de lire
        }
      }

      element.textContent = letters.slice(0, shown).join('');
      setTimeout(step, delay);
    }

    setTimeout(step, 2600);
  }

  function init(root) {
    root.querySelectorAll('[data-password-field]').forEach(function (field) {
      initPasswordToggle(field);
      initPasswordRules(field);
    });
    root.querySelectorAll('form[data-form]').forEach(initForm);
    root.querySelectorAll('[data-typewriter]').forEach(initTypewriter);

    var summary = root.querySelector('[data-error-summary]');
    if (summary) initErrorSummary(summary);
  }

  // Retour arrière : la page peut revenir du cache du navigateur, il faut réactiver les boutons.
  window.addEventListener('pageshow', function (event) {
    if (event.persisted) document.querySelectorAll('form[data-form]').forEach(resetForm);
  });

  init(document);

  // Pour l'aperçu de démonstration, qui affiche les pages sans recharger.
  window.Recital = { init: init, resetForm: resetForm, typewriter: initTypewriter };
})();
