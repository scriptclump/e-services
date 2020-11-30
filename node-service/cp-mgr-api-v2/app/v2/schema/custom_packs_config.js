/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('custom_packs_config', {
    epack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    cp_pack_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'custom_packs',
        key: 'cp_pack_id'
      }
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    product_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    effective_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'custom_packs_config'
  });
};
