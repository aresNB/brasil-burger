using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("quartiers")]
    public class Quartier
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("nom")]
        [MaxLength(100)]
        public string Nom { get; set; } = string.Empty;

        [Column("codepostal")]
        [MaxLength(10)]
        public string? CodePostal { get; set; }

        [Required]
        [Column("zoneid")]
        public int ZoneId { get; set; }

        // Relations
        [ForeignKey("ZoneId")]
        public Zone? Zone { get; set; }
    }
}