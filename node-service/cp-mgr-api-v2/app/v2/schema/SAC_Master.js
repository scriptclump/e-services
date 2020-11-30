/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('SAC_Master', {
    sac_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    sac_desc: {
      type: DataTypes.STRING(220),
      allowNull: true
    },
    tax_collection: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    other_receipts: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    deduct_refunds: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    penalties: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'SAC_Master'
  });
};
