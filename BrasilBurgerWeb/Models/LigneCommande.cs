using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("lignes_commande")]
    public class LigneCommande
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("quantite")]
        public int Quantite { get; set; }

        [Required]
        [Column("prixunitaire")]
        [DataType(DataType.Currency)]
        public decimal PrixUnitaire { get; set; }

        [Required]
        [Column("soustotal")]
        [DataType(DataType.Currency)]
        public decimal SousTotal { get; set; }

        [Required]
        [Column("typeproduit")]
        [MaxLength(20)]
        public string TypeProduit { get; set; } = string.Empty;

        [Required]
        [Column("commandeid")]
        public int CommandeId { get; set; }

        [Column("burgerid")]
        public int? BurgerId { get; set; }

        [Column("menuid")]
        public int? MenuId { get; set; }

        [Column("complementid")]
        public int? ComplementId { get; set; }

        // Relations
        [ForeignKey("CommandeId")]
        public Commande? Commande { get; set; }

        [ForeignKey("BurgerId")]
        public Burger? Burger { get; set; }

        [ForeignKey("MenuId")]
        public Menu? Menu { get; set; }

        [ForeignKey("ComplementId")]
        public Complement? Complement { get; set; }
    }
}