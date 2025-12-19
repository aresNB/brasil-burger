using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using BrasilBurgerWeb.Data;
using BrasilBurgerWeb.Models;

namespace BrasilBurgerWeb.Controllers
{
    public class CommandeController : Controller
    {
        private readonly ApplicationDbContext _context;

        public CommandeController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: Commande/Create?burgerId=5 ou ?menuId=3
        public async Task<IActionResult> Create(int? burgerId, int? menuId)
        {
            // Vérifier si l'utilisateur est connecté
            var userId = HttpContext.Session.GetInt32("UserId");
            if (userId == null)
            {
                TempData["ErrorMessage"] = "Vous devez être connecté pour commander.";
                return RedirectToAction("Login", "Auth");
            }

            // Charger le produit sélectionné
            if (burgerId.HasValue)
            {
                var burger = await _context.Burgers
                    .Include(b => b.Categorie)
                    .FirstOrDefaultAsync(b => b.Id == burgerId);

                if (burger == null)
                {
                    return NotFound();
                }

                ViewBag.Produit = burger;
                ViewBag.TypeProduit = "BURGER";
                ViewBag.PrixBase = burger.Prix;
            }
            else if (menuId.HasValue)
            {
                var menu = await _context.Menus
                    .Include(m => m.Burger)
                    .Include(m => m.Boisson)
                    .Include(m => m.Frite)
                    .FirstOrDefaultAsync(m => m.Id == menuId);

                if (menu == null)
                {
                    return NotFound();
                }

                ViewBag.Produit = menu;
                ViewBag.TypeProduit = "MENU";
                ViewBag.PrixBase = menu.PrixTotal;
            }
            else
            {
                return BadRequest("Aucun produit sélectionné.");
            }

            // Charger les compléments disponibles
            var boissons = await _context.Complements
                .Where(c => c.Type == "BOISSON" && !c.IsArchived)
                .ToListAsync();

            var frites = await _context.Complements
                .Where(c => c.Type == "FRITE" && !c.IsArchived)
                .ToListAsync();

            ViewBag.Boissons = boissons;
            ViewBag.Frites = frites;

            // Charger les zones pour la livraison
            var zones = await _context.Zones
                .Where(z => z.Actif)
                .ToListAsync();

            ViewBag.Zones = zones;

            return View();
        }

        // POST: Commande/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(
            int? burgerId,
            int? menuId,
            string modeConsommation,
            int? zoneId,
            string? adresseLivraison,
            List<int>? complementIds)
        {
            var userId = HttpContext.Session.GetInt32("UserId");
            if (userId == null)
            {
                return RedirectToAction("Login", "Auth");
            }

            // Calculer le montant total
            decimal montantTotal = 0;

            // Générer numéro de commande
            string numeroCommande = "CMD-" + DateTime.UtcNow.ToString("yyyyMMdd") + "-" +
                                    new Random().Next(1000, 9999);

            // Créer la commande
            var commande = new Commande
            {
                NumeroCommande = numeroCommande,
                DateCommande = DateTime.UtcNow,
                Etat = "EN_ATTENTE",
                ModeConsommation = modeConsommation,
                ClientId = userId.Value,
                ZoneId = (modeConsommation == "LIVRAISON") ? zoneId : null,
                AdresseLivraison = (modeConsommation == "LIVRAISON") ? adresseLivraison : null
            };

            _context.Commandes.Add(commande);
            await _context.SaveChangesAsync();

            // Ajouter la ligne de commande principale (burger ou menu)
            if (burgerId.HasValue)
            {
                var burger = await _context.Burgers.FindAsync(burgerId.Value);
                if (burger != null)
                {
                    var ligne = new LigneCommande
                    {
                        CommandeId = commande.Id,
                        BurgerId = burger.Id,
                        TypeProduit = "BURGER",
                        Quantite = 1,
                        PrixUnitaire = burger.Prix,
                        SousTotal = burger.Prix
                    };
                    _context.LignesCommande.Add(ligne);
                    montantTotal += burger.Prix;
                }
            }
            else if (menuId.HasValue)
            {
                var menu = await _context.Menus
                    .Include(m => m.Burger)
                    .Include(m => m.Boisson)
                    .Include(m => m.Frite)
                    .FirstOrDefaultAsync(m => m.Id == menuId);

                if (menu != null)
                {
                    var ligne = new LigneCommande
                    {
                        CommandeId = commande.Id,
                        MenuId = menu.Id,
                        TypeProduit = "MENU",
                        Quantite = 1,
                        PrixUnitaire = menu.PrixTotal,
                        SousTotal = menu.PrixTotal
                    };
                    _context.LignesCommande.Add(ligne);
                    montantTotal += menu.PrixTotal;
                }
            }

            // Ajouter les compléments sélectionnés
            if (complementIds != null && complementIds.Any())
            {
                foreach (var complementId in complementIds)
                {
                    var complement = await _context.Complements.FindAsync(complementId);
                    if (complement != null)
                    {
                        var ligne = new LigneCommande
                        {
                            CommandeId = commande.Id,
                            ComplementId = complement.Id,
                            TypeProduit = "COMPLEMENT",
                            Quantite = 1,
                            PrixUnitaire = complement.Prix,
                            SousTotal = complement.Prix
                        };
                        _context.LignesCommande.Add(ligne);
                        montantTotal += complement.Prix;
                    }
                }
            }

            // Ajouter les frais de livraison si nécessaire
            if (modeConsommation == "LIVRAISON" && zoneId.HasValue)
            {
                var zone = await _context.Zones.FindAsync(zoneId.Value);
                if (zone != null)
                {
                    montantTotal += zone.PrixLivraison;
                }
            }

            // Mettre à jour le montant total
            commande.MontantTotal = montantTotal;
            await _context.SaveChangesAsync();

            TempData["SuccessMessage"] = "Commande créée ! Veuillez procéder au paiement.";
            return RedirectToAction("Payer", "Paiement", new { commandeId = commande.Id });
        }

        // GET: Commande/MesCommandes
        public async Task<IActionResult> MesCommandes()
        {
            var userId = HttpContext.Session.GetInt32("UserId");
            if (userId == null)
            {
                return RedirectToAction("Login", "Auth");
            }

            var commandes = await _context.Commandes
                .Include(c => c.Zone)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Burger)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Menu)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Complement)
                .Where(c => c.ClientId == userId.Value)
                .OrderByDescending(c => c.DateCommande)
                .ToListAsync();

            return View(commandes);
        }

        // GET: Commande/Details/5
        public async Task<IActionResult> Details(int? id)
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

            var commande = await _context.Commandes
                .Include(c => c.Client)
                .Include(c => c.Zone)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Burger)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Menu)
                        .ThenInclude(m => m.Burger)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Menu)
                        .ThenInclude(m => m.Boisson)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Menu)
                        .ThenInclude(m => m.Frite)
                .Include(c => c.LignesCommande)
                    .ThenInclude(lc => lc.Complement)
                .FirstOrDefaultAsync(c => c.Id == id && c.ClientId == userId.Value);

            if (commande == null)
            {
                return NotFound();
            }

            return View(commande);
        }
    }
}