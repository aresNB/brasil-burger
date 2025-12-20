using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using BrasilBurgerWeb.Data;
using BrasilBurgerWeb.Models;

namespace BrasilBurgerWeb.Controllers
{
    public class PaiementController : Controller
    {
        private readonly ApplicationDbContext _context;

        public PaiementController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: Paiement/Payer?commandeId=5
        public async Task<IActionResult> Payer(int? commandeId)
        {
            if (commandeId == null)
            {
                return NotFound();
            }

            var userId = HttpContext.Session.GetInt32("UserId");
            if (userId == null)
            {
                return RedirectToAction("Login", "Auth");
            }

            var commande = await _context.Commandes
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Burger)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Menu)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Complement)
                .Include(c => c.Zone)
                .FirstOrDefaultAsync(c => c.Id == commandeId && c.ClientId == userId);

            if (commande == null)
            {
                return NotFound();
            }

            // Vérifier si la commande n'est pas déjà payée
            var paiementExistant = await _context.Paiements
                .FirstOrDefaultAsync(p => p.CommandeId == commande.Id);

            if (paiementExistant != null)
            {
                TempData["ErrorMessage"] = "Cette commande a déjà été payée.";
                return RedirectToAction("Details", "Commande", new { id = commande.Id });
            }

            return View(commande);
        }

        // POST: Paiement/Traiter
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Traiter(int commandeId, string moyenPaiement)
        {
            var userId = HttpContext.Session.GetInt32("UserId");
            if (userId == null)
            {
                return RedirectToAction("Login", "Auth");
            }

            var commande = await _context.Commandes
                .FirstOrDefaultAsync(c => c.Id == commandeId && c.ClientId == userId);

            if (commande == null)
            {
                return NotFound();
            }

            // Vérifier si déjà payée
            var paiementExistant = await _context.Paiements
                .FirstOrDefaultAsync(p => p.CommandeId == commande.Id);

            if (paiementExistant != null)
            {
                TempData["ErrorMessage"] = "Cette commande a déjà été payée.";
                return RedirectToAction("Details", "Commande", new { id = commande.Id });
            }

            // Générer une référence de transaction unique
            string refTransaction = moyenPaiement + "-" +
                                   DateTime.UtcNow.ToString("yyyyMMddHHmmss") + "-" +
                                   new Random().Next(1000, 9999);

            // Créer le paiement
            var paiement = new Paiement
            {
                CommandeId = commande.Id,
                Montant = commande.MontantTotal,
                MoyenPaiement = moyenPaiement,
                RefTransaction = refTransaction,
                Statut = "VALIDE",
                DatePaiement = DateTime.UtcNow
            };

            _context.Paiements.Add(paiement);

            // Mettre à jour l'état de la commande
            commande.Etat = "VALIDEE";
            commande.UpdatedAt = DateTime.UtcNow;

            await _context.SaveChangesAsync();

            TempData["SuccessMessage"] = "Paiement effectué avec succès !";
            return RedirectToAction("Success", new { id = paiement.Id });
        }

        // GET: Paiement/Success/5
        public async Task<IActionResult> Success(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var userId = HttpContext.Session.GetInt32("UserId");
            if (userId == null)
            {
                return RedirectToAction("Login", "Auth");
            }

            var paiement = await _context.Paiements
                .Include(p => p.Commande)
                    .ThenInclude(c => c.Client)
                .Include(p => p.Commande)
                    .ThenInclude(c => c.LignesCommande)
                        .ThenInclude(lc => lc.Burger)
                .Include(p => p.Commande)
                    .ThenInclude(c => c.LignesCommande)
                        .ThenInclude(lc => lc.Menu)
                .Include(p => p.Commande)
                    .ThenInclude(c => c.LignesCommande)
                        .ThenInclude(lc => lc.Complement)
                .FirstOrDefaultAsync(p => p.Id == id && p.Commande.ClientId == userId);

            if (paiement == null)
            {
                return NotFound();
            }

            return View(paiement);
        }

        // GET: Paiement/Echec?commandeId=5
        public async Task<IActionResult> Echec(int? commandeId)
        {
            if (commandeId == null)
            {
                return NotFound();
            }

            var commande = await _context.Commandes
                .FirstOrDefaultAsync(c => c.Id == commandeId);

            if (commande == null)
            {
                return NotFound();
            }

            ViewBag.CommandeId = commandeId;
            return View();
        }
    }
}