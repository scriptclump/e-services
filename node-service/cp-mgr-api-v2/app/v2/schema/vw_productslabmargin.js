/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_productslabmargin', {
    product_slab_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    start_range: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    end_range: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    price: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    business_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    margin: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.000000000'
    }
  }, {
    tableName: 'vw_productslabmargin'
  });
};
