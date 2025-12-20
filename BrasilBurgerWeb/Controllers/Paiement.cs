using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("paiements")]
    public class Paiement
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Column("datepaiement")]
        public DateTime DatePaiement { get; set; } = DateTime.UtcNow;

        [Required]
        [Column("montant")]
        [DataType(DataType.Currency)]
        public decimal Montant { get; set; }

        [Required]
        [Column("moyenpaiement")]
        [MaxLength(20)]
        public string MoyenPaiement { get; set; } = string.Empty; // WAVE ou OM

        [Required]
        [Column("reftransaction")]
        [MaxLength(100)]
        public string RefTransaction { get; set; } = string.Empty;

        [Column("statut")]
        [MaxLength(20)]
        public string Statut { get; set; } = "VALIDE";

        [Required]
        [Column("commandeid")]
        public int CommandeId { get; set; }

        // Relations
        [ForeignKey("CommandeId")]
        public Commande? Commande { get; set; }
    }
}