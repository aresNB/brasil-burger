using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("commandes")]
    public class Commande
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("numerocommande")]
        [MaxLength(50)]
        public string NumeroCommande { get; set; } = string.Empty;

        [Column("datecommande")]
        public DateTime DateCommande { get; set; } = DateTime.UtcNow;

        [Required]
        [Column("montanttotal")]
        [DataType(DataType.Currency)]
        public decimal MontantTotal { get; set; }

        [Required]
        [Column("etat")]
        [MaxLength(20)]
        public string Etat { get; set; } = "EN_ATTENTE";

        [Required]
        [Column("modeconsommation")]
        [MaxLength(20)]
        public string ModeConsommation { get; set; } = string.Empty;

        [Column("adresselivraison")]
        public string? AdresseLivraison { get; set; }

        [Required]
        [Column("clientid")]
        public int ClientId { get; set; }

        [Column("livreurid")]
        public int? LivreurId { get; set; }

        [Column("zoneid")]
        public int? ZoneId { get; set; }

        [Column("createdat")]
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        [Column("updatedat")]
        public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

        // Relations
        [ForeignKey("ClientId")]
        public User? Client { get; set; }

        [ForeignKey("LivreurId")]
        public User? Livreur { get; set; }

        [ForeignKey("ZoneId")]
        public Zone? Zone { get; set; }

        public ICollection<LigneCommande>? LignesCommande { get; set; }
    }
}