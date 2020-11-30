/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_products_status_counts', {
    active: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    disabled: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    open: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    allskus: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    Creation: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Approval: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Filling: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Enablement: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Drafted: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_products_status_counts'
  });
};
