/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('bin_type_dimensions', {
    bin_type_dim_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    bin_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    length: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    breadth: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    heigth: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    lbh_uom: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    },
    weigth: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    weigth_uom: {
      type: DataTypes.INTEGER(6),
      allowNull: true
    }
  }, {
    tableName: 'bin_type_dimensions'
  });
};
