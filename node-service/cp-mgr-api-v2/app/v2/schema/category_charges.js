/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('category_charges', {
    category_charges_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'categories',
        key: 'category_id'
      }
    },
    currrency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    charge_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    charges: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
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
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'category_charges'
  });
};
