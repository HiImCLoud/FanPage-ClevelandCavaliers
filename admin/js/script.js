(function ($) {
  "use strict";

  function handlePreloader() {
    if ($(".preloader").length) {
      $(".preloader").delay(200).fadeOut(500);
    }
  }
  function initHomeTable() {
    var table = new DataTable("#home-table", {
      layout: {
        topStart: {
          buttons: [],
        },
      },
      responsive: true,
    });
  }

  function initJerseysTable() {
    var table = new DataTable("#jerseys-table", {
      layout: {
        topStart: {
          buttons: [],
        },
      },
      responsive: true,
    });
  }

  function initPlayersTable() {
    var table = new DataTable("#players-table", {
      layout: {
        topStart: {
          buttons: [],
        },
      },
      responsive: true,
    });
  }

  function initAdminTable() {
    var table = new DataTable("#admin-table", {
      layout: {
        topStart: {
          buttons: [],
        },
      },
      responsive: true,
    });
  }

  $(document).ready(function () {
    $("#togglePasswordBtn").on("click", function () {
      const passwordInput = $("#passwordInput");
      const toggleIcon = $("#toggleIcon");

      if (passwordInput.attr("type") === "password") {
        passwordInput.attr("type", "text");
        toggleIcon.removeClass("bi-eye-slash").addClass("bi-eye");
      } else {
        passwordInput.attr("type", "password");
        toggleIcon.removeClass("bi-eye").addClass("bi-eye-slash");
      }
    });

    $("#toggleConfirmPasswordBtn").on("click", function () {
      const confirmPasswordInput = $("#confirmPasswordInput");
      const toggleIcon = $("#toggleConfirmIcon");

      if (confirmPasswordInput.attr("type") === "password") {
        confirmPasswordInput.attr("type", "text");
        toggleIcon.removeClass("bi-eye-slash").addClass("bi-eye");
      } else {
        confirmPasswordInput.attr("type", "password");
        toggleIcon.removeClass("bi-eye").addClass("bi-eye-slash");
      }
    });
  });

  $(document).on("click", ".editBtn", function () {
    $("#editMovieId").val($(this).data("id"));
    $("#editTitle").val($(this).data("title"));
    $("#editGenre").val($(this).data("genre"));
    $("#editActors").val($(this).data("actors"));
    $("#editYear").val($(this).data("year"));
    $("#editSynopsis").val($(this).data("synopsis"));
    $("#editLink").val($(this).data("link"));

    $("#editMovieModal").modal("show");
  });

  function init() {
    initHomeTable();
    initJerseysTable();
    initPlayersTable();
    initAdminTable();
  }
  $(window).on("load", function () {
    handlePreloader();
  });

  $(document).ready(init);
})(jQuery);
