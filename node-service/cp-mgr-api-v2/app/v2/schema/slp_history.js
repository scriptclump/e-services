/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('slp_history', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    supplier_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    supplier_name: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    slp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    effective_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'slp_history'
  });
};
