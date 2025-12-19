// using Microsoft.AspNetCore.Mvc;
// using Microsoft.EntityFrameworkCore;
// using BrasilBurgerWeb.Data;
// using BrasilBurgerWeb.Models;

// namespace BrasilBurgerWeb.Controllers
// {
//     public class CatalogueController : Controller
//     {
//         private readonly ApplicationDbContext _context;

//         public CatalogueController(ApplicationDbContext context)
//         {
//             _context = context;
//         }

//         // GET: Catalogue
//         public async Task<IActionResult> Index()
//         {
//             var burgers = await _context.Burgers
//                 .Include(b => b.Categorie)
//                 .Where(b => !b.IsArchived)
//                 .OrderBy(b => b.Libelle)
//                 .ToListAsync();

//             return View(burgers);
//         }

//         // GET: Catalogue/Details/5
//         public async Task<IActionResult> Details(int id)
//         {
//             var burger = await _context.Burgers
//                 .Include(b => b.Categorie)
//                 .FirstOrDefaultAsync(b => b.Id == id);

//             if (burger == null)
//                 return NotFound();

//             return View(burger);
//         }

//     }
// }


// =================================================

// using Microsoft.AspNetCore.Mvc;
// using Microsoft.EntityFrameworkCore;
// using BrasilBurgerWeb.Data;
// using BrasilBurgerWeb.Models;

// namespace BrasilBurgerWeb.Controllers
// {
//     public class CatalogueController : Controller
//     {
//         private readonly ApplicationDbContext _context;

//         public CatalogueController(ApplicationDbContext context)
//         {
//             _context = context;
//         }

//         // GET: Catalogue
//         public async Task<IActionResult> Index()
//         {
//             var burgers = await _context.Burgers
//                 .Include(b => b.Categorie)
//                 .Where(b => !b.IsArchived)
//                 .ToListAsync();

//             return View(burgers);
//         }

//         // GET: Catalogue/Details/5
//         public async Task<IActionResult> Details(int? id)
//         {
//             if (id == null)
//             {
//                 return NotFound();
//             }

//             var burger = await _context.Burgers
//                 .Include(b => b.Categorie)
//                 .FirstOrDefaultAsync(m => m.Id == id);

//             if (burger == null)
//             {
//                 return NotFound();
//             }

//             return View(burger);
//         }
//     }
// }


using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using BrasilBurgerWeb.Data;
using BrasilBurgerWeb.Models;

namespace BrasilBurgerWeb.Controllers
{
    public class CatalogueController : Controller
    {
        private readonly ApplicationDbContext _context;

        public CatalogueController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: Catalogue - Tous les produits
        public async Task<IActionResult> Index(string? filtre)
        {
            ViewBag.FiltreActif = filtre ?? "tous";

            if (filtre == "burgers")
            {
                return RedirectToAction(nameof(Burgers));
            }
            else if (filtre == "menus")
            {
                return RedirectToAction(nameof(Menus));
            }
            else if (filtre == "complements")
            {
                return RedirectToAction(nameof(Complements));
            }

            // Par défaut : tous les produits
            var burgers = await _context.Burgers
                .Include(b => b.Categorie)
                .Where(b => !b.IsArchived)
                .ToListAsync();

            return View(burgers);
        }

        // GET: Catalogue/Burgers
        public async Task<IActionResult> Burgers()
        {
            ViewBag.FiltreActif = "burgers";
            var burgers = await _context.Burgers
                .Include(b => b.Categorie)
                .Where(b => !b.IsArchived)
                .ToListAsync();

            return View("Index", burgers);
        }

        // GET: Catalogue/Menus
        public async Task<IActionResult> Menus()
        {
            ViewBag.FiltreActif = "menus";
            var menus = await _context.Menus
                .Include(m => m.Burger)
                .Include(m => m.Boisson)
                .Include(m => m.Frite)
                .Where(m => !m.IsArchived)
                .ToListAsync();

            return View("ListeMenus", menus);
        }

        // GET: Catalogue/Complements
        public async Task<IActionResult> Complements()
        {
            ViewBag.FiltreActif = "complements";
            var complements = await _context.Complements
                .Where(c => !c.IsArchived)
                .ToListAsync();

            return View("ListeComplements", complements);
        }

        // GET: Catalogue/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var burger = await _context.Burgers
                .Include(b => b.Categorie)
                .FirstOrDefaultAsync(m => m.Id == id);

            if (burger == null)
            {
                return NotFound();
            }

            return View(burger);
        }

        // GET: Catalogue/DetailsMenu/5
        public async Task<IActionResult> DetailsMenu(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var menu = await _context.Menus
                .Include(m => m.Burger)
                .Include(m => m.Boisson)
                .Include(m => m.Frite)
                .FirstOrDefaultAsync(m => m.Id == id);

            if (menu == null)
            {
                return NotFound();
            }

            return View(menu);
        }
    }
}