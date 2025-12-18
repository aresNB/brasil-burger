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

        // GET: Catalogue
        public async Task<IActionResult> Index()
        {
            var burgers = await _context.Burgers
                .Include(b => b.Categorie)
                .Where(b => !b.IsArchived)
                .OrderBy(b => b.Libelle)
                .ToListAsync();

            return View(burgers);
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
                .FirstOrDefaultAsync(m => m.Id == id && !m.IsArchived);

            if (burger == null)
            {
                return NotFound();
            }

            return View(burger);
        }
    }
}