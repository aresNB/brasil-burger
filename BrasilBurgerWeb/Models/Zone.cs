using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("zones")]
    public class Zone
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("nom")]
        [MaxLength(100)]
        public string Nom { get; set; } = string.Empty;

        [Required]
        [Column("prixlivraison")]
        [DataType(DataType.Currency)]
        public decimal PrixLivraison { get; set; }

        [Column("actif")]
        public bool Actif { get; set; } = true;

        // Relations
        public ICollection<Quartier>? Quartiers { get; set; }
    }
}