using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using BrasilBurgerWeb.Data;
using BrasilBurgerWeb.Models;

namespace BrasilBurgerWeb.Controllers
{
    public class AuthController : Controller
    {
        private readonly ApplicationDbContext _context;

        public AuthController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: Auth/Register
        public IActionResult Register()
        {
            return View();
        }

        // POST: Auth/Register
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Register(User user)
        {
            if (ModelState.IsValid)
            {
                // Vérifier si l'email existe déjà
                var existingUser = await _context.Users
                    .FirstOrDefaultAsync(u => u.Email == user.Email);

                if (existingUser != null)
                {
                    ModelState.AddModelError("Email", "Cet email est déjà utilisé.");
                    return View(user);
                }

                // Vérifier si le téléphone existe déjà
                var existingTel = await _context.Users
                    .FirstOrDefaultAsync(u => u.Tel == user.Tel);

                if (existingTel != null)
                {
                    ModelState.AddModelError("Tel", "Ce numéro de téléphone est déjà utilisé.");
                    return View(user);
                }

                // Définir le rôle par défaut
                user.Role = "CLIENT";
                user.CreatedAt = DateTime.UtcNow;

                // Note: En production, il faut HASHER le mot de passe !
                // Pour ce projet, on garde le mot de passe en clair (à des fins pédagogiques)

                _context.Add(user);
                await _context.SaveChangesAsync();

                // Connecter automatiquement l'utilisateur
                HttpContext.Session.SetInt32("UserId", user.Id);
                HttpContext.Session.SetString("UserNom", user.Nom);
                HttpContext.Session.SetString("UserPrenom", user.Prenom);
                HttpContext.Session.SetString("UserRole", user.Role);

                TempData["SuccessMessage"] = "Compte créé avec succès ! Bienvenue " + user.Prenom + " !";
                return RedirectToAction("Index", "Catalogue");
            }

            return View(user);
        }

        // GET: Auth/Login
        public IActionResult Login()
        {
            return View();
        }

        // POST: Auth/Login
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Login(string email, string password)
        {
            if (string.IsNullOrEmpty(email) || string.IsNullOrEmpty(password))
            {
                ViewBag.ErrorMessage = "Veuillez remplir tous les champs.";
                return View();
            }

            var user = await _context.Users
                .FirstOrDefaultAsync(u => u.Email == email && u.Password == password);

            if (user == null)
            {
                ViewBag.ErrorMessage = "Email ou mot de passe incorrect.";
                return View();
            }

            // Créer la session
            HttpContext.Session.SetInt32("UserId", user.Id);
            HttpContext.Session.SetString("UserNom", user.Nom);
            HttpContext.Session.SetString("UserPrenom", user.Prenom);
            HttpContext.Session.SetString("UserRole", user.Role);

            TempData["SuccessMessage"] = "Connexion réussie ! Bienvenue " + user.Prenom + " !";
            return RedirectToAction("Index", "Catalogue");
        }

        // GET: Auth/Logout
        public IActionResult Logout()
        {
            HttpContext.Session.Clear();
            TempData["SuccessMessage"] = "Vous avez été déconnecté avec succès.";
            return RedirectToAction("Index", "Catalogue");
        }
    }
}